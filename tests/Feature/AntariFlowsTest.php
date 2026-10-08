<?php

namespace Tests\Feature;

use App\Enums\DisputeStatus;
use App\Enums\DocumentType;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Category;
use App\Models\District;
use App\Models\ProviderProfile;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Review;
use App\Models\ReviewDispute;
use App\Models\ReviewReply;
use App\Models\User;
use App\Models\VerificationDocument;
use Database\Seeders\DemoProviderReviewSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Tests\TestCase;

class AntariFlowsTest extends TestCase
{
    use RefreshDatabase;

    private User $client;

    private User $provider;

    private User $admin;

    private ProviderProfile $profile;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create(['name' => 'Guru Les', 'slug' => 'guru-les', 'description' => 'Tutor lokal']);
        Province::create(['code' => '34', 'name' => 'DI Yogyakarta']);
        Province::create(['code' => '36', 'name' => 'Banten']);
        Regency::create(['code' => '34.04', 'province_code' => '34', 'name' => 'Kabupaten Sleman']);
        Regency::create(['code' => '36.01', 'province_code' => '36', 'name' => 'Kabupaten Pandeglang']);
        District::create(['code' => '34.04.02', 'regency_code' => '34.04', 'name' => 'Godean']);
        District::create(['code' => '36.01.01', 'regency_code' => '36.01', 'name' => 'Sumur']);
        $this->client = $this->createUser(UserRole::CLIENT, true);
        $this->provider = $this->createUser(UserRole::PROVIDER, false);
        $this->admin = $this->createUser(UserRole::ADMIN, true);

        $this->profile = ProviderProfile::create([
            'user_id' => $this->provider->id,
            'category_id' => $category->id,
            'province_code' => '34',
            'regency_code' => '34.04',
            'district_code' => '34.04.02',
            'title' => 'Tutor Matematika',
            'bio' => 'Pengajar berpengalaman di Yogyakarta.',
            'starting_price' => 75000,
            'whatsapp_number' => '081234567890',
            'address' => 'Sleman, Yogyakarta',
            'latitude' => -7.7686,
            'longitude' => 110.3781,
        ]);
    }

    public function test_public_search_and_provider_profile_are_rendered(): void
    {
        $this->assertStringContainsString(
            '/providers/provider-user',
            route('providers.show', $this->profile),
        );

        $home = $this->get(route('home', ['q' => 'Tutor']))
            ->assertOk()
            ->assertSee('Tutor Matematika')
            ->assertSee($this->provider->name)
            ->assertSee('data-turbo-frame="provider-results"', false)
            ->assertSee('<turbo-frame id="provider-results"', false);

        $homeHtml = $home->getContent();
        $locationPosition = strpos($homeHtml, 'Godean, Kabupaten Sleman');
        $pricePosition = strpos($homeHtml, 'Tarif mulai dari');

        $this->assertNotFalse($locationPosition);
        $this->assertNotFalse($pricePosition);
        $this->assertLessThan($pricePosition, $locationPosition);
        $this->assertStringNotContainsString('Portofolio '.$this->provider->name, $homeHtml);

        $this->get(route('providers.show', $this->profile))
            ->assertOk()
            ->assertSee('Hubungi via WhatsApp')
            ->assertSee('Belum ada aktivitas')
            ->assertSee('Provider belum mengunggah foto pekerjaan.');
    }

    public function test_provider_slugs_are_unique_and_follow_account_name_changes(): void
    {
        $anotherProvider = $this->createUser(UserRole::PROVIDER, false);
        $anotherProvider->update(['name' => $this->provider->name]);
        $anotherProfile = ProviderProfile::create([
            'user_id' => $anotherProvider->id,
            'category_id' => $this->profile->category_id,
            'title' => 'Jasa lain',
            'whatsapp_number' => '081299988877',
        ]);

        $this->assertSame('provider-user-2', $anotherProfile->slug);

        $this->actingAs($anotherProvider)
            ->patch(route('profile.update'), [
                'name' => 'Siti Rahmawati',
                'email' => $anotherProvider->email,
            ])
            ->assertRedirect(route('profile.edit'));

        $this->assertSame('siti-rahmawati', $anotherProfile->fresh()->slug);
        $this->get(route('providers.show', $anotherProfile->fresh()))
            ->assertOk()
            ->assertSee('Jasa lain');
    }

    public function test_demo_provider_reviews_are_seeded_by_the_verified_demo_client_idempotently(): void
    {
        $client = User::updateOrCreate(
            ['email' => 'budi.client@antari.id'],
            [
                'name' => 'Budi Santoso',
                'password' => 'password123',
                'role' => UserRole::CLIENT,
                'is_verified' => true,
                'email_verified_at' => now(),
            ],
        );

        foreach (range(1, 100) as $number) {
            $user = User::create([
                'name' => 'Demo Provider '.$number,
                'email' => sprintf('provider.demo.%03d@sekita.id', $number),
                'password' => 'password123',
                'role' => UserRole::PROVIDER,
            ]);

            ProviderProfile::create([
                'user_id' => $user->id,
                'category_id' => $this->profile->category_id,
                'province_code' => '34',
                'regency_code' => '34.04',
                'district_code' => '34.04.02',
                'title' => 'Jasa demo '.$number,
                'whatsapp_number' => '081200000000',
            ]);
        }

        (new DemoProviderReviewSeeder)->run();

        $this->assertSame(100, Review::query()->where('client_id', $client->id)->count());
        $this->assertDatabaseHas('reviews', [
            'client_id' => $client->id,
            'comment' => 'Contoh ulasan demo: komunikasi jelas dan hasil pekerjaan sesuai kesepakatan.',
        ]);

        (new DemoProviderReviewSeeder)->run();

        $this->assertSame(100, Review::query()->where('client_id', $client->id)->count());
        $this->assertSame(100, ProviderProfile::query()->where('total_reviews', '>', 0)->count());
    }

    public function test_login_failure_is_displayed_in_a_top_alert_in_indonesian(): void
    {
        $this->from(route('login'))
            ->post(route('login'), [
                'email' => 'unknown@example.test',
                'password' => 'incorrect-password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('role="alert"', false)
            ->assertSee('Email atau kata sandi yang Anda masukkan salah.')
            ->assertDontSee('route=login');
    }

    public function test_provider_workspace_tabs_and_dispute_evidence_field_render(): void
    {
        $this->profile->reviews()->create([
            'client_id' => $this->client->id,
            'rating' => 5,
            'comment' => 'Pengalaman layanan yang baik.',
            'is_published' => true,
        ]);

        $this->actingAs($this->provider)
            ->get(route('provider.dashboard'))
            ->assertOk()
            ->assertSee(route('provider.profile.edit'), false)
            ->assertSee(route('provider.documents.index'), false)
            ->assertSee(route('provider.reviews.index'), false);

        $this->assertFalse(Route::has('provider.portfolio.index'));
        $this->assertFalse(Route::has('provider.portfolio.store'));
        $this->assertFalse(Route::has('provider.portfolio.destroy'));

        $this->actingAs($this->provider)
            ->get(route('provider.profile.edit'))
            ->assertOk()
            ->assertSee('Foto profil & galeri', false);

        $this->actingAs($this->provider)
            ->get(route('provider.reviews.index'))
            ->assertOk()
            ->assertSee('name="evidence_details" rows="4"', false);
    }

    public function test_admin_can_delete_provider_and_cascaded_records_and_files(): void
    {
        Storage::fake('private_documents');
        Storage::fake('public');

        $privatePath = $this->provider->id.'/KTP/identity.pdf';
        $portfolioPath = 'portfolios/'.$this->provider->id.'/work.webp';
        Storage::disk('private_documents')->put($privatePath, 'private identity');
        Storage::disk('public')->put($portfolioPath, 'portfolio image');

        VerificationDocument::create([
            'user_id' => $this->provider->id,
            'document_type' => DocumentType::KTP,
            'private_file_url' => $privatePath,
            'status' => VerificationStatus::PENDING,
        ]);
        $gallery = $this->profile->portfolioGalleries()->create(['image_url' => $portfolioPath]);
        $review = $this->profile->reviews()->create([
            'client_id' => $this->client->id,
            'rating' => 5,
            'comment' => 'Ulasan yang akan ikut terhapus bersama profil.',
            'is_published' => true,
        ]);
        ReviewReply::create([
            'review_id' => $review->id,
            'provider_profile_id' => $this->profile->id,
            'reply_text' => 'Terima kasih atas ulasannya.',
        ]);
        ReviewDispute::create([
            'review_id' => $review->id,
            'reporter_id' => $this->provider->id,
            'reason' => 'Laporan terkait ulasan.',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Manajemen pengguna')
            ->assertSee('Hapus Akun')
            ->assertSee($this->provider->email);

        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $this->provider))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', 'Akun pengguna berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $this->provider->id]);
        $this->assertDatabaseMissing('provider_profiles', ['id' => $this->profile->id]);
        $this->assertDatabaseMissing('verification_documents', ['user_id' => $this->provider->id]);
        $this->assertDatabaseMissing('portfolio_galleries', ['id' => $gallery->id]);
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
        $this->assertDatabaseMissing('review_replies', ['review_id' => $review->id]);
        $this->assertDatabaseMissing('review_disputes', ['review_id' => $review->id]);
        Storage::disk('private_documents')->assertMissing($privatePath);
        Storage::disk('public')->assertMissing($portfolioPath);
    }

    public function test_admin_user_deletion_is_role_restricted_and_cannot_delete_admin_accounts(): void
    {
        $this->actingAs($this->client)
            ->delete(route('admin.users.destroy', $this->provider))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $this->admin))
            ->assertNotFound();

        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_can_delete_client_and_cascade_their_reviews(): void
    {
        $review = $this->profile->reviews()->create([
            'client_id' => $this->client->id,
            'rating' => 4,
            'comment' => 'Ulasan client yang dihapus.',
            'is_published' => true,
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $this->client))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('status', 'Akun pengguna berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $this->client->id]);
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_location_search_filters_to_selected_administrative_area(): void
    {
        $farProvider = $this->createUser(UserRole::PROVIDER, false);
        ProviderProfile::create([
            'user_id' => $farProvider->id,
            'category_id' => $this->profile->category_id,
            'province_code' => '36',
            'regency_code' => '36.01',
            'district_code' => '36.01.01',
            'title' => 'Teknisi jauh',
            'whatsapp_number' => '081299988877',
        ]);

        $this->get(route('home', [
            'province_code' => '34',
            'regency_code' => '34.04',
            'district_code' => '34.04.02',
        ]))
            ->assertOk()
            ->assertSee('Tutor Matematika')
            ->assertDontSee('Teknisi jauh')
            ->assertSee('Godean');
    }

    public function test_location_endpoints_return_only_children_of_the_selected_parent(): void
    {
        $this->get(route('locations.regencies', '34'))
            ->assertOk()
            ->assertJsonPath('data.0.code', '34.04');

        $this->get(route('locations.districts', '34.04'))
            ->assertOk()
            ->assertJsonPath('data.0.code', '34.04.02');
    }

    public function test_provider_profile_edit_contains_gallery_upload_and_no_linkedin_field(): void
    {
        $this->actingAs($this->provider)
            ->get(route('provider.profile.edit'))
            ->assertOk()
            ->assertSee('Foto profil & galeri', false)
            ->assertSee('Unggah foto')
            ->assertDontSee('LinkedIn URL');
    }

    public function test_provider_profile_requires_a_consistent_province_city_district_chain(): void
    {
        $payload = [
            'category_id' => $this->profile->category_id,
            'province_code' => '34',
            'regency_code' => '34.04',
            'district_code' => '34.04.02',
            'title' => 'Tutor Matematika',
            'bio' => 'Pengajar berpengalaman.',
            'starting_price' => 75000,
            'whatsapp_number' => '081234567890',
            'service_radius_km' => 20,
            'address' => 'Area Sleman',
        ];

        $this->actingAs($this->provider)
            ->patch(route('provider.profile.update'), $payload)
            ->assertRedirect();

        $this->assertSame('34.04.02', $this->profile->fresh()->district_code);
        $this->assertDatabaseMissing('provider_profiles', ['service_radius_km' => 20]);

        $payload['district_code'] = '36.01.01';
        $this->actingAs($this->provider)
            ->patch(route('provider.profile.update'), $payload)
            ->assertStatus(422)
            ->assertSee('kecamatan yang dipilih tidak valid.');
    }

    public function test_provider_search_applies_database_pagination(): void
    {
        for ($index = 0; $index < 14; $index++) {
            $provider = $this->createUser(UserRole::PROVIDER, false);
            ProviderProfile::create([
                'user_id' => $provider->id,
                'category_id' => $this->profile->category_id,
                'province_code' => '34',
                'regency_code' => '34.04',
                'district_code' => '34.04.02',
                'title' => 'Provider '.$index,
                'whatsapp_number' => '081200000000',
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->get(route('home'))->assertOk();
        $paginator = $response->viewData('providers');

        $this->assertSame(12, $paginator->count());
        $this->assertSame(15, $paginator->total());
        $this->assertStringContainsString('#providers', $paginator->url(2));
        $response->assertSee('Berikutnya');

        $this->get(route('home', ['page' => 2]))
            ->assertOk()
            ->assertSee('Sebelumnya');

        $providerSelect = collect(DB::getQueryLog())->first(fn (array $query): bool => str_contains(strtolower($query['query']), 'provider_profiles')
            && preg_match('/\blimit\s+12\b/i', $query['query']) === 1
        );

        $this->assertNotNull($providerSelect, 'Provider rows should be limited by the database query.');
    }

    public function test_dashboard_redirect_and_role_guards_are_enforced(): void
    {
        $this->actingAs($this->provider)
            ->get(route('dashboard'))
            ->assertRedirect(route('provider.dashboard'));

        $this->actingAs($this->client)
            ->get(route('provider.dashboard'))
            ->assertForbidden();

        $this->actingAs($this->provider)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_provider_activity_updates_last_online_and_public_profile_shows_relative_status(): void
    {
        $this->actingAs($this->provider)
            ->get(route('provider.dashboard'))
            ->assertOk();

        $this->assertNotNull($this->provider->fresh()->last_online_at);
        $this->get(route('providers.show', $this->profile))
            ->assertOk()
            ->assertSee('Aktif Sekarang');

        DB::table('users')->where('id', $this->provider->id)->update([
            'last_online_at' => now()->subHours(2),
        ]);

        $this->get(route('providers.show', $this->profile))
            ->assertOk()
            ->assertSee('Aktif 2 jam yang lalu');
    }

    public function test_review_filter_returns_422_and_contact_details_are_removed(): void
    {
        $this->actingAs($this->client)
            ->post(route('reviews.store', $this->profile), [
                'rating' => 1,
                'comment' => 'Penyedia ini penipu dan tidak jujur.',
            ])
            ->assertStatus(422);

        $this->actingAs($this->client)
            ->post(route('reviews.store', $this->profile), [
                'rating' => 5,
                'comment' => 'Datang tepat waktu dan hasilnya rapi. Hubungi 081234567890 atau https://promo.example',
            ])
            ->assertRedirect();

        $review = Review::query()->firstOrFail();
        $this->assertStringNotContainsString('081234567890', $review->comment);
        $this->assertStringNotContainsString('https://', $review->comment);
        $this->assertSame(1, $this->profile->fresh()->total_reviews);

        $this->from(route('providers.show', $this->profile))
            ->post(route('reviews.store', $this->profile), [
                'rating' => 4,
                'comment' => 'Ulasan kedua untuk penyedia yang sama.',
            ])
            ->assertSessionHasErrors('comment');

        $this->assertSame(1, Review::query()->count());
    }

    public function test_client_can_edit_and_delete_own_review_within_thirty_days_without_resetting_limit(): void
    {
        $review = $this->profile->reviews()->create([
            'client_id' => $this->client->id,
            'rating' => 3,
            'comment' => 'Pengalaman cukup baik dan pengerjaan sesuai jadwal.',
            'is_published' => true,
        ]);
        $originalCreatedAt = $review->created_at;

        $this->actingAs($this->client)
            ->patch(route('client.reviews.update', $review), [
                'rating' => 5,
                'comment' => 'Hasil pekerjaan rapi dan komunikasinya sangat baik.',
            ])
            ->assertRedirect();

        $review->refresh();
        $this->assertSame(5, $review->rating);
        $this->assertSame($originalCreatedAt->toDateTimeString(), $review->created_at->toDateTimeString());
        $this->assertSame(5.0, $this->profile->fresh()->avg_rating);

        $otherClient = $this->createUser(UserRole::CLIENT, true);
        $this->actingAs($otherClient)
            ->patch(route('client.reviews.update', $review), [
                'rating' => 1,
                'comment' => 'Mencoba mengubah review milik pengguna lain.',
            ])
            ->assertNotFound();

        $this->actingAs($this->client)
            ->delete(route('client.reviews.destroy', $review))
            ->assertRedirect();

        $this->assertSoftDeleted('reviews', ['id' => $review->id]);
        $this->assertSame(0, $this->profile->fresh()->total_reviews);

        $this->from(route('providers.show', $this->profile))
            ->post(route('reviews.store', $this->profile), [
                'rating' => 4,
                'comment' => 'Mencoba mengirim review setelah menghapus review awal.',
            ])
            ->assertSessionHasErrors('comment');
    }

    public function test_client_cannot_edit_or_delete_review_after_thirty_day_window(): void
    {
        $review = $this->profile->reviews()->create([
            'client_id' => $this->client->id,
            'rating' => 4,
            'comment' => 'Layanan selesai dengan baik dan sesuai permintaan.',
            'is_published' => true,
        ]);
        $review->forceFill(['created_at' => now()->subDays(31)])->save();

        $this->actingAs($this->client)
            ->patch(route('client.reviews.update', $review), [
                'rating' => 5,
                'comment' => 'Mencoba mengubah review setelah melewati masa edit.',
            ])
            ->assertSessionHasErrors('comment');

        $this->actingAs($this->client)
            ->delete(route('client.reviews.destroy', $review))
            ->assertSessionHasErrors('comment');

        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'deleted_at' => null]);
    }

    public function test_provider_registration_creates_provider_profile_without_admin_escalation(): void
    {
        $this->post(route('register'), [
            'name' => '<b>Calon Provider</b>',
            'email' => 'new.provider@example.test',
            'phone' => '081298765432',
            'account_type' => 'PROVIDER',
            'password' => 'password12345',
            'password_confirmation' => 'password12345',
        ])->assertRedirect(route('dashboard'));

        $user = User::query()->where('email', 'new.provider@example.test')->firstOrFail();
        $this->assertSame('Calon Provider', $user->name);
        $this->assertSame(UserRole::PROVIDER, $user->role);
        $this->assertNotNull($user->providerProfile);

        $this->post(route('logout'));
        $this->post(route('register'), [
            'name' => 'Admin Palsu',
            'email' => 'fake.admin@example.test',
            'account_type' => 'ADMIN',
            'password' => 'password12345',
            'password_confirmation' => 'password12345',
        ])->assertSessionHasErrors('account_type');
    }

    public function test_email_verification_enables_client_reviews(): void
    {
        $unverifiedClient = $this->createUser(UserRole::CLIENT, false);
        $verificationUrl = URL::temporarySignedRoute('verification.verify', now()->addMinutes(10), [
            'id' => $unverifiedClient->id,
            'hash' => sha1($unverifiedClient->getEmailForVerification()),
        ]);

        $this->actingAs($unverifiedClient)
            ->get($verificationUrl)
            ->assertRedirect(route('dashboard').'?verified=1');

        $this->assertTrue($unverifiedClient->fresh()->is_verified);
        $this->assertNotNull($unverifiedClient->fresh()->email_verified_at);

        $this->post(route('reviews.store', $this->profile), [
            'rating' => 5,
            'comment' => 'Pengalaman baik dan hasil pekerjaan rapi.',
        ])->assertRedirect();

        $this->assertDatabaseHas('reviews', ['client_id' => $unverifiedClient->id]);
    }

    public function test_portfolio_upload_is_converted_to_webp_and_limited_to_six(): void
    {
        Storage::fake('public');

        $this->actingAs($this->provider)
            ->post(route('provider.profile.portfolio.store'), [
                'image' => UploadedFile::fake()->image('work.jpg', 1800, 1200),
                'caption' => 'Pekerjaan selesai',
            ])
            ->assertRedirect();

        $imagePath = $this->profile->portfolioGalleries()->firstOrFail()->image_url;
        $this->assertStringEndsWith('.webp', $imagePath);
        $this->assertTrue(Storage::disk('public')->exists($imagePath));

        $this->profile->portfolioGalleries()->delete();
        for ($index = 0; $index < 6; $index++) {
            $this->profile->portfolioGalleries()->create(['image_url' => "work/{$index}.webp"]);
        }

        $this->actingAs($this->provider)
            ->post(route('provider.profile.portfolio.store'), [
                'image' => UploadedFile::fake()->image('seventh.jpg'),
            ])
            ->assertStatus(422);
    }

    public function test_only_admin_can_download_private_documents_and_verification_updates_badges(): void
    {
        Storage::fake('private_documents');
        $path = 'documents/'.$this->provider->id.'/ktp.pdf';
        Storage::disk('private_documents')->put($path, 'private sample');
        $document = VerificationDocument::create([
            'user_id' => $this->provider->id,
            'document_type' => DocumentType::KTP,
            'private_file_url' => $path,
            'status' => VerificationStatus::PENDING,
        ]);
        VerificationDocument::create([
            'user_id' => $this->provider->id,
            'document_type' => DocumentType::IJAZAH,
            'private_file_url' => 'documents/'.$this->provider->id.'/ijazah.pdf',
            'status' => VerificationStatus::PENDING,
        ]);

        $this->actingAs($this->provider)
            ->get(route('admin.documents.download', $document))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->get(route('admin.documents.download', $document))
            ->assertOk()
            ->assertDownload('KTP_user_'.$this->provider->id.'.pdf');

        $this->actingAs($this->admin)
            ->get(route('admin.documents.index'))
            ->assertOk()
            ->assertSee('Diajukan')
            ->assertSee('Menunggu keputusan');

        $filteredDocuments = $this->get(route('admin.documents.index', ['type' => DocumentType::KTP->value]))
            ->assertOk()
            ->assertSee($this->provider->email)
            ->assertSee('Semua')
            ->assertSee('KTP');
        $this->assertSame(1, substr_count($filteredDocuments->getContent(), '<tr class="align-top">'));

        $this->patch(route('admin.documents.update', $document), ['status' => 'VERIFIED'])
            ->assertRedirect();

        $this->assertTrue($this->profile->fresh()->id_verified_badge);
        $this->assertTrue($this->provider->fresh()->is_verified);
        $this->assertNotNull($document->fresh()->reviewed_at);
    }

    public function test_provider_uploads_documents_to_private_storage(): void
    {
        Storage::fake('private_documents');

        $this->actingAs($this->provider)
            ->post(route('provider.documents.store'), [
                'document_type' => 'IJAZAH',
                'document' => UploadedFile::fake()->create('ijazah.pdf', 250, 'application/pdf'),
            ])
            ->assertRedirect();

        $document = VerificationDocument::query()->where('user_id', $this->provider->id)->firstOrFail();
        $this->assertSame(VerificationStatus::PENDING, $document->status);
        $this->assertTrue(Storage::disk('private_documents')->exists($document->private_file_url));
        $this->assertStringNotContainsString('/storage/', $document->private_file_url);
    }

    public function test_admin_approval_hides_review_and_rejection_keeps_it_published(): void
    {
        $review = $this->profile->reviews()->create([
            'client_id' => $this->client->id,
            'rating' => 1,
            'comment' => 'Pengalaman kurang baik, layanan tidak sesuai jadwal.',
            'is_published' => true,
        ]);
        $dispute = ReviewDispute::create([
            'review_id' => $review->id,
            'reporter_id' => $this->provider->id,
            'reason' => 'Ulasan tidak sesuai fakta',
            'status' => DisputeStatus::UNDER_REVIEW,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.disputes.index'))
            ->assertOk()
            ->assertSee('Diajukan')
            ->assertSee('Menunggu keputusan')
            ->assertSee('Disetujui · disembunyikan')
            ->assertSee('Ditolak · dipertahankan');

        $this->actingAs($this->admin)
            ->patch(route('admin.disputes.update', $dispute), ['status' => 'APPROVED'])
            ->assertRedirect();

        $this->get(route('admin.disputes.index'))
            ->assertOk()
            ->assertSee('Disetujui · review disembunyikan')
            ->assertSee('Keputusan admin');

        $this->assertFalse($review->fresh()->is_published);
        $this->assertSame(0, $this->profile->fresh()->total_reviews);
        $this->assertNotNull($dispute->fresh()->resolved_at);

        $this->actingAs($this->client)
            ->patch(route('client.reviews.update', $review), [
                'rating' => 2,
                'comment' => 'Saya memperbarui detail pengalaman setelah moderasi.',
            ])
            ->assertRedirect();

        $this->assertFalse($review->fresh()->is_published);
        $this->assertSame(0, $this->profile->fresh()->total_reviews);
    }

    public function test_admin_dispute_status_tabs_filter_their_results(): void
    {
        foreach ([
            [DisputeStatus::UNDER_REVIEW, 'pending marker'],
            [DisputeStatus::APPROVED, 'approved marker'],
            [DisputeStatus::REJECTED, 'rejected marker'],
        ] as [$status, $comment]) {
            $review = $this->profile->reviews()->create([
                'client_id' => User::create([
                    'name' => $comment,
                    'email' => Str::uuid().'@antari.test',
                    'password' => Hash::make('password123'),
                    'role' => UserRole::CLIENT,
                    'is_verified' => true,
                    'email_verified_at' => now(),
                ])->id,
                'rating' => 4,
                'comment' => 'Review content for '.$comment,
                'is_published' => $status !== DisputeStatus::APPROVED,
            ]);

            ReviewDispute::create([
                'review_id' => $review->id,
                'reporter_id' => $this->provider->id,
                'reason' => $comment,
                'status' => $status,
            ]);
        }

        $this->actingAs($this->admin)
            ->get(route('admin.disputes.index', ['status' => DisputeStatus::APPROVED->value]))
            ->assertOk()
            ->assertSee('approved marker')
            ->assertDontSee('pending marker')
            ->assertDontSee('rejected marker');
    }

    private function createUser(UserRole $role, bool $verified): User
    {
        $name = ucfirst(strtolower($role->value)).' User';

        return User::create([
            'name' => $name,
            'email' => Str::uuid().'@antari.test',
            'phone' => '081200000000',
            'password' => Hash::make('password123'),
            'role' => $role,
            'is_verified' => $verified,
            'email_verified_at' => $verified ? now() : null,
        ]);
    }
}
