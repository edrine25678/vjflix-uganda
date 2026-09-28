<?php

namespace Database\Seeders;

use App\Models\Episode;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Movie;
use App\Models\Role;
use App\Models\Season;
use App\Models\Series;
use App\Models\User;
use App\Models\Vj;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PlanSeeder::class,
        ]);
        // 1. Roles
        $roles = [
            ['name' => 'super_admin', 'label' => 'Super Administrator', 'description' => 'Full system access and billing control'],
            ['name' => 'content_manager', 'label' => 'Content Manager', 'description' => 'Can manage movies, series, episodes and media'],
            ['name' => 'vj_manager', 'label' => 'VJ Manager', 'description' => 'Can manage VJ profiles and assign translation credits'],
            ['name' => 'subscriber', 'label' => 'Paid Subscriber', 'description' => 'Active streaming subscription holder'],
            ['name' => 'user', 'label' => 'Standard User', 'description' => 'Standard registered viewer'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(['name' => $roleData['name']], $roleData);
        }

        $superAdminRole = Role::where('name', 'super_admin')->first();
        $userRole = Role::where('name', 'user')->first();

        // 2. Default Users
        $admin = User::updateOrCreate(
            ['email' => 'admin@vjflix.ug'],
            [
                'name' => 'VJFlix Admin',
                'username' => 'admin',
                'password' => 'password',
                'role' => 'super_admin',
                'preferred_language' => 'Luganda',
                'is_active' => true,
            ]
        );
        $admin->roles()->syncWithoutDetaching([$superAdminRole->id]);

        // Keep legacy test credentials working as well
        $legacyAdmin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'System Admin',
                'username' => 'sysadmin',
                'password' => 'password',
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );
        $legacyAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);

        $viewer = User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Kato Paul',
                'username' => 'katopaul',
                'password' => 'password',
                'role' => 'user',
                'preferred_language' => 'Luganda',
                'is_active' => true,
            ]
        );
        $viewer->roles()->syncWithoutDetaching([$userRole->id]);

        // 3. Languages
        $languages = [
            ['name' => 'Luganda', 'code' => 'lg'],
            ['name' => 'English', 'code' => 'en'],
            ['name' => 'Runyankole-Rukiga', 'code' => 'nyn'],
            ['name' => 'Lugbara', 'code' => 'lgg'],
            ['name' => 'Swahili', 'code' => 'sw'],
            ['name' => 'Ateso', 'code' => 'teo'],
        ];
        foreach ($languages as $lang) {
            Language::firstOrCreate(['code' => $lang['code']], $lang);
        }

        // 4. Genres
        $genresData = [
            ['name' => 'Action', 'slug' => 'action', 'description' => 'High octane battles, combat, and blockbusters'],
            ['name' => 'Comedy', 'slug' => 'comedy', 'description' => 'Hilarious comedy with sharp Luganda punchlines'],
            ['name' => 'Luganda Special', 'slug' => 'luganda-special', 'description' => 'Specially curated translations with full cultural flavor'],
            ['name' => 'Sci-Fi & Fantasy', 'slug' => 'sci-fi-fantasy', 'description' => 'Futuristic worlds, technology, and super powers'],
            ['name' => 'Nollywood & Africa', 'slug' => 'nollywood-africa', 'description' => 'Top tier African cinema and dramatic sagas'],
            ['name' => 'Thriller', 'slug' => 'thriller', 'description' => 'Suspenseful plots, crime mysteries, and heist sagas'],
            ['name' => 'Horror', 'slug' => 'horror', 'description' => 'Spooky thrills and supernatural mysteries'],
            ['name' => 'Animation', 'slug' => 'animation', 'description' => 'Animated family adventures and anime'],
            ['name' => 'Drama', 'slug' => 'drama', 'description' => 'Emotional and deep narrative stories'],
        ];

        $genreModels = [];
        foreach ($genresData as $g) {
            $genreModels[$g['slug']] = Genre::firstOrCreate(['slug' => $g['slug']], $g);
        }

        // 5. Ugandan Video Jockeys (VJs)
        $vjsData = [
            [
                'name' => 'Maryane Kasujja',
                'stage_name' => 'VJ Junior',
                'slug' => 'vj-junior',
                'biography' => 'Regarded as the pioneer and undisputed heavyweight of Ugandan movie narration. Renowned for thrilling action explanations, unmatched timing, and signature character voices.',
                'specialization' => 'Hollywood Blockbusters, Action, Sci-Fi',
                'is_verified' => true,
                'views_count' => 1250000,
                'rating' => 4.95,
                'profile_photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'name' => 'Ismail Jingo',
                'stage_name' => 'VJ Jingo',
                'slug' => 'vj-jingo',
                'biography' => 'Veteran Ugandan narrator with over two decades on the microphone. Famous for classic martial arts, deep dramatic stories, and legendary proverbs.',
                'specialization' => 'Martial Arts, Drama, Historical Epics',
                'is_verified' => true,
                'views_count' => 890000,
                'rating' => 4.88,
                'profile_photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'name' => 'Emmanuel Ssenyonjo',
                'stage_name' => 'VJ Emmy',
                'slug' => 'vj-emmy',
                'biography' => 'The favorite among Korean drama and modern thriller enthusiasts. Fast-paced delivery, humorous commentary, and rapid translation turnaround.',
                'specialization' => 'K-Dramas, Modern Crime Thrillers, Comedy',
                'is_verified' => true,
                'views_count' => 740000,
                'rating' => 4.82,
                'profile_photo' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'name' => 'Peter Mukasa',
                'stage_name' => 'VJ Ice P',
                'slug' => 'vj-ice-p',
                'biography' => 'Specialist in Chinese Kung Fu cinema, tactical action, and mind-bending thrillers. Uniquely energetic narration style.',
                'specialization' => 'Chinese Kung Fu, Warfare, Crime',
                'is_verified' => true,
                'views_count' => 520000,
                'rating' => 4.75,
                'profile_photo' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'name' => 'Mark Mutebi',
                'stage_name' => 'VJ Mark',
                'slug' => 'vj-mark',
                'biography' => 'The voice of Bollywood romance, musicals, and family drama across Kampala and beyond.',
                'specialization' => 'Bollywood, Romantic Drama, Family',
                'is_verified' => true,
                'views_count' => 430000,
                'rating' => 4.70,
                'profile_photo' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=400&q=80',
            ],
            [
                'name' => 'Kevin Lubega',
                'stage_name' => 'VJ Kevo',
                'slug' => 'vj-kevo',
                'biography' => 'Energetic translation sensation bringing modern animation, superhero movies, and horror films to young audiences.',
                'specialization' => 'Superhero, Animation, Horror',
                'is_verified' => true,
                'views_count' => 380000,
                'rating' => 4.68,
                'profile_photo' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=400&q=80',
            ],
        ];

        $vjModels = [];
        foreach ($vjsData as $vj) {
            $vjModels[$vj['slug']] = Vj::updateOrCreate(['slug' => $vj['slug']], $vj);
        }

        // 6. Realistic Movies with VJ Translations
        $moviesData = [
            [
                'title' => 'John Wick: Chapter 4 (Luganda)',
                'slug' => 'john-wick-chapter-4-luganda',
                'original_title' => 'John Wick: Chapter 4',
                'description' => 'John Wick uncovers a path to defeating the High Table. But before he can earn his freedom, Wick must face off against a new enemy.',
                'synopsis' => 'Translated by VJ Junior with breathless tactical precision and unforgettable Kampala street humor. Follow Baba Yaga as he battles assassins across Osaka, Berlin, and Paris.',
                'poster' => 'https://image.tmdb.org/t/p/w500/vZloFAK7NKnMGKEHvYcnEtvhn0e.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/original/7I6VUdPj6tQECNHdviJkUHD2389.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=qEVUtrk8_B4',
                'duration' => 169,
                'release_year' => 2023,
                'vj_id' => $vjModels['vj-junior']->id,
                'country_of_origin' => 'United States',
                'age_rating' => '18+',
                'status' => 'published',
                'featured' => true,
                'trending' => true,
                'views' => 148200,
                'average_rating' => 4.95,
                'ratings_count' => 3820,
                'genres' => ['action', 'thriller', 'luganda-special'],
            ],
            [
                'title' => 'Avatar: The Way of Water (Luganda)',
                'slug' => 'avatar-the-way-of-water-luganda',
                'original_title' => 'Avatar: The Way of Water',
                'description' => 'Jake Sully lives with his newfound family formed on the extrasolar moon Pandora. Once a familiar threat returns to finish what was previously started, Jake must work with Neytiri and the army of the Na\'vi race to protect their home.',
                'synopsis' => 'VJ Junior brings James Cameron\'s ocean masterpiece alive in Luganda, giving emotional weight and epic translation to every reef battle and tulkun call.',
                'poster' => 'https://image.tmdb.org/t/p/w500/t6HIqrRAclMCA60NsSmeqe9RmNV.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/original/s16H6tpK2utvwDtzZ8Qy4qm5Emw.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=d9MyW72ELq0',
                'duration' => 192,
                'release_year' => 2022,
                'vj_id' => $vjModels['vj-junior']->id,
                'country_of_origin' => 'United States',
                'age_rating' => 'PG-13',
                'status' => 'published',
                'featured' => true,
                'trending' => true,
                'views' => 215000,
                'average_rating' => 4.91,
                'ratings_count' => 4150,
                'genres' => ['sci-fi-fantasy', 'action', 'luganda-special'],
            ],
            [
                'title' => 'Ip Man: The Final Fight (Luganda)',
                'slug' => 'ip-man-the-final-fight-luganda',
                'original_title' => 'Ip Man: The Final Fight',
                'description' => 'In postwar Hong Kong, legendary Wing Chun grandmaster Ip Man is reluctantly drawn into action once more.',
                'synopsis' => 'Classic martial arts cinema narrated by the master of Wing Chun commentary, VJ Jingo. Hear every bone break and punch decoded with classic Luganda proverbs.',
                'poster' => 'https://image.tmdb.org/t/p/w500/bOH6k2p5xM61zT3U2tV9wS9xK6M.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/original/uLtV5o5LNX99WsvVoLiAIf94Bug.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=1AJxXQ74QGY',
                'duration' => 100,
                'release_year' => 2013,
                'vj_id' => $vjModels['vj-jingo']->id,
                'country_of_origin' => 'Hong Kong',
                'age_rating' => 'PG-13',
                'status' => 'published',
                'featured' => false,
                'trending' => true,
                'views' => 98400,
                'average_rating' => 4.88,
                'ratings_count' => 1940,
                'genres' => ['action', 'drama', 'luganda-special'],
            ],
            [
                'title' => 'The Roundup: No Way Out (Luganda)',
                'slug' => 'the-roundup-no-way-out-luganda',
                'original_title' => 'Beomjoe dosi 3',
                'description' => 'Monster Cop Ma Seok-do heads to a new police unit to investigate a mysterious drug syndicate and corrupt yakuza dealers.',
                'synopsis' => 'Don Lee\'s thunderous punches narrated by VJ Emmy. Fast, aggressive, comedic, and action-packed.',
                'poster' => 'https://image.tmdb.org/t/p/w500/lP5IsbFhhZ8kH81u1pE9Y4rK1o9.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/original/eG0oOQxJge9stV22hiJoyIZHIj5.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=7uKqZ7G9N1o',
                'duration' => 105,
                'release_year' => 2023,
                'vj_id' => $vjModels['vj-emmy']->id,
                'country_of_origin' => 'South Korea',
                'age_rating' => '16+',
                'status' => 'published',
                'featured' => false,
                'trending' => true,
                'views' => 84000,
                'average_rating' => 4.84,
                'ratings_count' => 1430,
                'genres' => ['action', 'comedy', 'thriller'],
            ],
            [
                'title' => 'Spider-Man: Across the Spider-Verse (Luganda)',
                'slug' => 'spider-man-across-the-spider-verse-luganda',
                'original_title' => 'Spider-Man: Across the Spider-Verse',
                'description' => 'Miles Morales catapults across the Multiverse, where he encounters a team of Spider-People charged with protecting its very existence.',
                'synopsis' => 'VJ Kevo brings high-energy youthful slang to Miles Morales\' multidimensional journey. Pure family entertainment in Luganda.',
                'poster' => 'https://image.tmdb.org/t/p/w500/8Vt6mWEReuy4Of61Lnj5Xj704m8.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/original/4HodYYKEIsGOdinkGi2Ucz6X9i0.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=cqGjhVJWtEg',
                'duration' => 140,
                'release_year' => 2023,
                'vj_id' => $vjModels['vj-kevo']->id,
                'country_of_origin' => 'United States',
                'age_rating' => 'PG',
                'status' => 'published',
                'featured' => true,
                'trending' => false,
                'views' => 112000,
                'average_rating' => 4.90,
                'ratings_count' => 2200,
                'genres' => ['animation', 'action', 'sci-fi-fantasy'],
            ],
            [
                'title' => 'Pathaan (Luganda)',
                'slug' => 'pathaan-luganda',
                'original_title' => 'Pathaan',
                'description' => 'An Indian secret agent embarks on a mission to stop a private terrorist organization from unleashing a deadly biological weapon.',
                'synopsis' => 'Shah Rukh Khan\'s blockbuster return narrated with passion and high drama by VJ Mark. Musical sequences and grand scale action translated for Ugandan fans.',
                'poster' => 'https://image.tmdb.org/t/p/w500/m1b9ToJim5WNB9O74Ju4uhmR6rG.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/original/mSyQvAhnB0fG9C3LdDkP18nJpQe.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=vqu4z34wENw',
                'duration' => 146,
                'release_year' => 2023,
                'vj_id' => $vjModels['vj-mark']->id,
                'country_of_origin' => 'India',
                'age_rating' => '16+',
                'status' => 'published',
                'featured' => false,
                'trending' => false,
                'views' => 76000,
                'average_rating' => 4.76,
                'ratings_count' => 1120,
                'genres' => ['action', 'thriller', 'drama'],
            ],
            [
                'title' => 'Evil Dead Rise (Luganda)',
                'slug' => 'evil-dead-rise-luganda',
                'original_title' => 'Evil Dead Rise',
                'description' => 'A twisted tale of two estranged sisters whose reunion is cut short by the rise of flesh-possessing demons.',
                'synopsis' => 'Terrifying horror movie translated by VJ Ice P. Brace yourself for bone-chilling shocks and screams rendered vividly in Luganda.',
                'poster' => 'https://image.tmdb.org/t/p/w500/5qW8lWzB3O0p7w8yH5j1B1M1k5I.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/original/7bWxAsNPv9vdAh6AqPL0umHGUtx.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=smTK_AeAPHs',
                'duration' => 96,
                'release_year' => 2023,
                'vj_id' => $vjModels['vj-ice-p']->id,
                'country_of_origin' => 'United States',
                'age_rating' => '18+',
                'status' => 'published',
                'featured' => false,
                'trending' => false,
                'views' => 67000,
                'average_rating' => 4.65,
                'ratings_count' => 980,
                'genres' => ['horror', 'thriller'],
            ],
            [
                'title' => 'Anikulapo (Luganda)',
                'slug' => 'anikulapo-luganda',
                'original_title' => 'Aníkúlápó',
                'description' => 'After an affair with a queen leads to his demise, an eager traveler encounters a mystical bird with the power to give him another life.',
                'synopsis' => 'Kunle Afolayan\'s Nigerian fantasy drama translated by VJ Jingo. Rich folklore and African spiritual history translated with cultural authenticity.',
                'poster' => 'https://image.tmdb.org/t/p/w500/5A3w5rG4nLq1H7yZ1u8oE7g5X8.jpg',
                'backdrop' => 'https://image.tmdb.org/t/p/original/9i7g3cK1uY6oA5Z7X1r4e3q9a0b.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=3gB2T8_f3zM',
                'duration' => 142,
                'release_year' => 2022,
                'vj_id' => $vjModels['vj-jingo']->id,
                'country_of_origin' => 'Nigeria',
                'age_rating' => '16+',
                'status' => 'published',
                'featured' => false,
                'trending' => false,
                'views' => 92000,
                'average_rating' => 4.80,
                'ratings_count' => 1650,
                'genres' => ['nollywood-africa', 'drama', 'sci-fi-fantasy'],
            ],
        ];

        foreach ($moviesData as $mData) {
            $genreSlugs = $mData['genres'];
            unset($mData['genres']);

            $movie = Movie::updateOrCreate(['slug' => $mData['slug']], $mData);

            $genreIds = [];
            foreach ($genreSlugs as $gSlug) {
                if (isset($genreModels[$gSlug])) {
                    $genreIds[] = $genreModels[$gSlug]->id;
                }
            }
            $movie->genres()->sync($genreIds);
        }

        // 6. TV Series, Seasons, and Episodes translated by Ugandan VJs
        $seriesData = [
            [
                'title' => 'Money Heist (Luganda)',
                'slug' => 'money-heist-luganda',
                'synopsis' => 'An unusual group of robbers attempt to carry out the most perfect robbery in Spanish history, translated with high intensity and sharp slang by VJ Junior.',
                'description' => 'To carry out the biggest heist in history, a mysterious man called The Professor recruits a band of eight robbers who have a single characteristic: none of them has anything to lose. VJ Junior elevates every scene with legendary Luganda dubbing, punchy catchphrases, and rapid translations.',
                'poster' => 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?auto=format&fit=crop&w=600&q=80',
                'backdrop' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&w=1200&q=80',
                'vj_id' => $vjModels['vj-junior']->id,
                'first_air_year' => 2021,
                'status' => 'published',
                'featured' => true,
                'trending' => true,
                'views' => 380000,
                'average_rating' => 4.95,
                'published_at' => now(),
                'genres' => ['action', 'crime', 'drama'],
                'seasons' => [
                    [
                        'season_number' => 1,
                        'title' => 'Part 1: The Royal Mint',
                        'overview' => 'The Professor orchestrates the infiltration into the Royal Mint of Spain.',
                        'release_year' => 2021,
                        'episodes' => [
                            [
                                'episode_number' => 1,
                                'title' => 'Ekitundu 1: Omulimu Gutandise (Do as Planned)',
                                'overview' => 'The Professor recruits Tokyo, Berlin, Nairobi, Rio, Denver, Moscow, Helsinki, and Oslo.',
                                'duration' => 47,
                                'views' => 140000,
                                'is_free' => true,
                            ],
                            [
                                'episode_number' => 2,
                                'title' => 'Ekitundu 2: Okulumba kwa Poliisi (Lethal Mistake)',
                                'overview' => 'Hostage negotiator Raquel makes first contact with The Professor while gunfire erupts outside.',
                                'duration' => 42,
                                'views' => 110000,
                                'is_free' => false,
                            ],
                            [
                                'episode_number' => 3,
                                'title' => 'Ekitundu 3: Okulwana kw\'omu Mint (Misfire)',
                                'overview' => 'Police storm the entrance perimeter as Berlin initiates contingency Protocol 4.',
                                'duration' => 50,
                                'views' => 95000,
                                'is_free' => false,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Prison Break: The Escape (Luganda)',
                'slug' => 'prison-break-luganda',
                'synopsis' => 'Michael Scofield enters Fox River State Penitentiary with the blueprints tattooed on his body, masterfully narrated by VJ Jingo.',
                'description' => 'Due to a political conspiracy, an innocent man is sent to death row and his only hope is his brother, who makes it his mission to deliberately get himself sent to the same prison in order to break the both of them out from the inside. VJ Jingo delivers classic old-school proverbs and thrilling play-by-play commentary.',
                'poster' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=600&q=80',
                'backdrop' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=1200&q=80',
                'vj_id' => $vjModels['vj-jingo']->id,
                'first_air_year' => 2020,
                'status' => 'published',
                'featured' => false,
                'trending' => true,
                'views' => 290000,
                'average_rating' => 4.90,
                'published_at' => now(),
                'genres' => ['action', 'crime', 'drama'],
                'seasons' => [
                    [
                        'season_number' => 1,
                        'title' => 'Season 1: Fox River Infiltration',
                        'overview' => 'Michael maps every inch of the prison pipelines and builds his crew.',
                        'release_year' => 2020,
                        'episodes' => [
                            [
                                'episode_number' => 1,
                                'title' => 'Ekitundu 1: Okuyingira Ekomera (Pilot)',
                                'overview' => 'Michael Scofield stages a bank robbery in Chicago to get sentenced to Fox River.',
                                'duration' => 44,
                                'views' => 135000,
                                'is_free' => true,
                            ],
                            [
                                'episode_number' => 2,
                                'title' => 'Ekitundu 2: Entalo z\'omu Kkomera (Allen)',
                                'overview' => 'T-Bag sparks racial tension in the courtyard while Michael hunts for an Allen bolt.',
                                'duration' => 43,
                                'views' => 105000,
                                'is_free' => false,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Squid Game (Luganda)',
                'slug' => 'squid-game-luganda',
                'synopsis' => 'Hundreds of cash-strapped players accept a strange invitation to compete in children\'s games with deadly stakes. Voiced by VJ Emmy.',
                'description' => 'Hundreds of desperate contestants accept an invitation to compete in traditional playground games for a 45.6 billion won jackpot, but the stakes are fatal. VJ Emmy brings his signature rapid-fire comedic punchlines and nail-biting suspense to this Korean phenomenon.',
                'poster' => 'https://images.unsplash.com/photo-1509198397868-475647b2a1e5?auto=format&fit=crop&w=600&q=80',
                'backdrop' => 'https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&w=1200&q=80',
                'vj_id' => $vjModels['vj-emmy']->id,
                'first_air_year' => 2022,
                'status' => 'published',
                'featured' => false,
                'trending' => true,
                'views' => 310000,
                'average_rating' => 4.88,
                'published_at' => now(),
                'genres' => ['action', 'drama', 'sci-fi-fantasy'],
                'seasons' => [
                    [
                        'season_number' => 1,
                        'title' => 'Season 1: Survival Games',
                        'overview' => 'Contestants are trapped on an isolated island facing ruthless childhood games.',
                        'release_year' => 2022,
                        'episodes' => [
                            [
                                'episode_number' => 1,
                                'title' => 'Ekitundu 1: Akazannyo k\'Omunyeera (Red Light, Green Light)',
                                'overview' => 'Player 456 enters the arena and faces the deadly giant robot doll.',
                                'duration' => 59,
                                'views' => 160000,
                                'is_free' => true,
                            ],
                            [
                                'episode_number' => 2,
                                'title' => 'Ekitundu 2: Obulamu bw\'Ensi (Hell)',
                                'overview' => 'After voting to leave, players realize life outside is worse and return to the island.',
                                'duration' => 62,
                                'views' => 125000,
                                'is_free' => false,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'All of Us Are Dead (Luganda)',
                'slug' => 'all-of-us-are-dead-luganda',
                'synopsis' => 'A high school becomes ground zero for a zombie virus outbreak. Translated by VJ Ice P.',
                'description' => 'Trapped high school students must fight their way out or turn into one of the rabid infected. VJ Ice P injects non-stop thrill and fierce Ugandan translations into every corridor fight.',
                'poster' => 'https://images.unsplash.com/photo-1509281373149-e957c6296406?auto=format&fit=crop&w=600&q=80',
                'backdrop' => 'https://images.unsplash.com/photo-1485846234645-a62644f84728?auto=format&fit=crop&w=1200&q=80',
                'vj_id' => $vjModels['vj-ice-p']->id,
                'first_air_year' => 2023,
                'status' => 'published',
                'featured' => false,
                'trending' => false,
                'views' => 180000,
                'average_rating' => 4.79,
                'published_at' => now(),
                'genres' => ['action', 'sci-fi-fantasy', 'horror'],
                'seasons' => [
                    [
                        'season_number' => 1,
                        'title' => 'Season 1: Hyosan High Outbreak',
                        'overview' => 'Students barricade themselves in classrooms as infection spreads like wildfire.',
                        'release_year' => 2023,
                        'episodes' => [
                            [
                                'episode_number' => 1,
                                'title' => 'Ekitundu 1: Obulwadde Butandise (Infection Starts)',
                                'overview' => 'A bitten student collapses in the science lab and turns violently aggressive.',
                                'duration' => 53,
                                'views' => 95000,
                                'is_free' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($seriesData as $sData) {
            $genreSlugs = $sData['genres'];
            $seasonsData = $sData['seasons'];
            unset($sData['genres'], $sData['seasons']);

            $series = Series::updateOrCreate(['slug' => $sData['slug']], $sData);

            $genreIds = [];
            foreach ($genreSlugs as $gSlug) {
                if (isset($genreModels[$gSlug])) {
                    $genreIds[] = $genreModels[$gSlug]->id;
                }
            }
            $series->genres()->sync($genreIds);

            foreach ($seasonsData as $seasonData) {
                $episodesData = $seasonData['episodes'];
                unset($seasonData['episodes']);

                $season = Season::updateOrCreate(
                    ['series_id' => $series->id, 'season_number' => $seasonData['season_number']],
                    array_merge($seasonData, ['series_id' => $series->id])
                );

                foreach ($episodesData as $epData) {
                    Episode::updateOrCreate(
                        ['season_id' => $season->id, 'episode_number' => $epData['episode_number']],
                        array_merge($epData, [
                            'season_id' => $season->id,
                            'vj_id' => $series->vj_id,
                            'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                        ])
                    );
                }
            }
        }
    }
}
