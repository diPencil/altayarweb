<?php

namespace Tests\Feature\Api\Mobile;

use App\Models\Listing;
use App\Models\ListingType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OfferActionsTest extends TestCase
{
    use RefreshDatabase;

    private function createListing(): Listing
    {
        $listingType = ListingType::query()->create([
            'name' => 'Featured Offers',
            'status' => 1,
        ]);

        return Listing::query()->create([
            'title' => 'Test Offer',
            'slug' => 'test-offer-' . uniqid(),
            'listing_type_id' => $listingType->id,
            'type' => 'offer',
            'summary' => 'Test summary',
            'description' => 'Test description',
            'price' => 100,
            'discount' => 0,
            'status' => 1,
        ]);
    }

    public function test_guest_add_favorite_returns_401(): void
    {
        $listing = $this->createListing();

        $response = $this->postJson('/api/mobile/offers/' . $listing->id . '/favorite');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_add_and_remove_favorite(): void
    {
        $user = User::query()->create([
            'name' => 'Test User Favorite',
            'email' => 'favorite-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);
        Sanctum::actingAs($user);
        $listing = $this->createListing();

        $addResponse = $this->postJson('/api/mobile/offers/' . $listing->id . '/favorite');

        $addResponse->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Offer added to favorites',
                'is_favorite' => true,
            ]);

        $this->assertDatabaseHas('listing_favorites', [
            'user_id' => $user->id,
            'listing_id' => $listing->id,
        ]);

        $removeResponse = $this->deleteJson('/api/mobile/offers/' . $listing->id . '/favorite');

        $removeResponse->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Offer removed from favorites',
                'is_favorite' => false,
            ]);

        $this->assertDatabaseMissing('listing_favorites', [
            'user_id' => $user->id,
            'listing_id' => $listing->id,
        ]);
    }

    public function test_authenticated_user_cannot_rate_with_invalid_rating(): void
    {
        $user = User::query()->create([
            'name' => 'Test User Rating Invalid',
            'email' => 'rating-invalid-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);
        Sanctum::actingAs($user);
        $listing = $this->createListing();

        $response = $this->postJson('/api/mobile/offers/' . $listing->id . '/rate', [
            'rating' => 6,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rating']);
    }

    public function test_authenticated_user_can_rate_offer(): void
    {
        $user = User::query()->create([
            'name' => 'Test User Rating Valid',
            'email' => 'rating-valid-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);
        Sanctum::actingAs($user);
        $listing = $this->createListing();

        $response = $this->postJson('/api/mobile/offers/' . $listing->id . '/rate', [
            'rating' => 5,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Rating submitted',
                'rating' => 5,
                'average_rating' => 5,
                'ratings_count' => 1,
            ]);

        $this->assertDatabaseHas('listing_ratings', [
            'user_id' => $user->id,
            'listing_id' => $listing->id,
            'rating' => 5,
        ]);
    }
}