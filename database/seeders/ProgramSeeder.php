<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

/** The 14 primary objectives of the foundation, each as a Program. */
class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->programs() as $i => $p) {
            Program::firstOrCreate(['slug' => $p['slug']], [
                'title' => $p['title'],
                'icon' => $p['icon'],
                'excerpt' => $p['excerpt'],
                'description' => '<p>'.implode('</p><p>', $p['body']).'</p>',
                'highlights' => implode("\n", $p['highlights']),
                'image' => $p['image'] ?? null,
                'sort_order' => $i + 1,
                'is_featured' => $i < 8,
                'is_active' => true,
                'meta_description' => $p['excerpt'],
            ]);
        }
    }

    private function programs(): array
    {
        return [
            [
                'title' => 'Education & Skills', 'slug' => 'education-and-skills', 'icon' => 'mortarboard', 'image' => 'seed/photos/photo1.webp',
                'excerpt' => 'Literacy, digital skills, vocational training and personality development for children, youth, women and senior citizens.',
                'body' => [
                    'Education is the first step of every <em>udaan</em>. We run learning support classes for children who are falling behind, help first-generation learners stay in school, and bring books, bags and stationery to families who cannot afford them.',
                    'For young people and women, we offer digital literacy, spoken English, vocational and skill-development courses that open the door to jobs and self-employment. Life-skills and personality-development sessions build confidence, communication and leadership — and our learning is open to senior citizens too, because it is never too late to learn.',
                ],
                'highlights' => ['Remedial and evening classes for school children', 'Free books, bags and stationery for children in need', 'Computer and digital literacy training', 'Vocational courses — tailoring, beauty, electrical and more', 'Life skills, personality development and career guidance', 'Adult and senior-citizen literacy'],
            ],
            [
                'title' => 'Healthcare', 'slug' => 'healthcare', 'icon' => 'heart-pulse', 'image' => 'seed/photos/photo2.webp',
                'excerpt' => 'Free medical camps, preventive health awareness, sanitation, hygiene, nutrition, mental health awareness and first-aid training.',
                'body' => [
                    'Many families in rural Gonda live far from a doctor. Our free medical camps bring check-ups, basic tests and medicines to the village doorstep, with referrals for those who need further care.',
                    'Prevention is just as important as cure. We run awareness sessions on hygiene, sanitation, nutrition, menstrual health and mental well-being, and train community members in first aid so that help is always close at hand.',
                ],
                'highlights' => ['Free general health check-up camps', 'Eye, dental and blood-pressure / sugar screening', 'Nutrition awareness for mothers and children', 'Menstrual hygiene and sanitation drives', 'Mental health awareness sessions', 'First-aid training for youth and volunteers'],
            ],
            [
                'title' => 'Women Empowerment & Social Welfare', 'slug' => 'women-empowerment', 'icon' => 'gender-female', 'image' => 'seed/photos/photo3.webp',
                'excerpt' => 'Gender equality, self-help groups and community development for women, children, youth, senior citizens and differently-abled persons.',
                'body' => [
                    '<strong>Atmanirbhar Nari, Atmanirbhar Bharat</strong> — self-reliant women build a self-reliant nation. We organise women into self-help groups, train them in savings, credit and small enterprise, and connect them with government schemes and markets.',
                    'Alongside livelihoods, we raise awareness on gender equality, legal rights and safety, and work for the welfare and inclusion of children, youth, senior citizens and differently-abled persons in community life.',
                ],
                'highlights' => ['Formation and training of women’s self-help groups', 'Financial literacy and micro-enterprise support', 'Awareness of rights, safety and government schemes', 'Leadership programmes for young women', 'Support and inclusion of differently-abled persons'],
            ],
            [
                'title' => 'Care Homes & Centres', 'slug' => 'care-homes-and-centres', 'icon' => 'house-heart', 'image' => 'seed/photos/photo6.webp',
                'excerpt' => 'Homes, child-care centres, day-care and community centres for children, senior citizens and differently-abled persons.',
                'body' => [
                    'Some people need more than occasional help — they need a safe, caring place. We work towards establishing and supporting homes, child-care and day-care centres, and community centres where children, senior citizens and differently-abled persons are looked after with dignity.',
                    'These centres also serve as hubs for learning, health check-ups and recreation for the surrounding community.',
                ],
                'highlights' => ['Child-care and day-care centres', 'Support for elderly people living alone', 'Community and recreation centres', 'Care and therapy support for differently-abled persons'],
            ],
            [
                'title' => 'Animal Welfare', 'slug' => 'animal-welfare', 'icon' => 'feather',
                'excerpt' => 'Care, shelter, feeding, treatment and rescue of animals and birds, and humane treatment of every living being.',
                'body' => [
                    'Compassion does not stop with people. We organise feeding drives for stray animals, support treatment and vaccination, rescue injured animals and birds, and help provide shelter.',
                    'Through awareness in schools and neighbourhoods, we promote the humane treatment of animals and birds — especially during harsh summers and winters.',
                ],
                'highlights' => ['Feeding drives for stray animals', 'Rescue and treatment of injured animals and birds', 'Water bowls and shelter during extreme weather', 'Awareness on humane treatment of animals'],
            ],
            [
                'title' => 'Food & Relief', 'slug' => 'food-and-relief', 'icon' => 'basket', 'image' => 'seed/photos/photo5.webp',
                'excerpt' => 'Food, clothing, safe drinking water, essential items and community kitchens for those in need.',
                'body' => [
                    'No one should go to bed hungry or cold. We distribute food, ration kits, clothing and blankets to families in need, and work towards community kitchens that serve nutritious meals.',
                    'We also support access to safe drinking water and essential items for the most vulnerable households.',
                ],
                'highlights' => ['Ration-kit and food distribution', 'Winter blanket and clothing drives', 'Community kitchen meals', 'Safe drinking water initiatives'],
            ],
            [
                'title' => 'Environment', 'slug' => 'environment', 'icon' => 'tree', 'image' => 'seed/photos/photo4.webp',
                'excerpt' => 'Afforestation, cleanliness, waste management, clean rivers, water conservation, renewable energy and climate action.',
                'body' => [
                    'A better tomorrow needs a healthy planet. Our volunteers plant and care for trees, organise cleanliness and waste-segregation drives, and work for cleaner rivers and ponds.',
                    'We promote water conservation, renewable energy and biodiversity, and help young people understand climate change and what they can do about it.',
                ],
                'highlights' => ['Tree plantation and care drives', 'Cleanliness and waste-management campaigns', 'Clean river and pond initiatives', 'Water conservation and rain-water harvesting awareness', 'Renewable energy and climate-action education'],
            ],
            [
                'title' => 'Civic & Community Development', 'slug' => 'civic-and-community-development', 'icon' => 'buildings',
                'excerpt' => 'Public welfare, sanitation, public amenities, slum-area development and citizen participation for better neighbourhoods.',
                'body' => [
                    'Strong communities are built by active citizens. We work with residents and local authorities to improve sanitation, public amenities and living conditions in underserved and slum areas.',
                    'Through neighbourhood meetings and campaigns, we encourage citizen participation in improving public spaces and accessing public services.',
                ],
                'highlights' => ['Neighbourhood improvement drives', 'Sanitation and public amenity support', 'Citizen awareness and participation campaigns', 'Coordination with local authorities'],
            ],
            [
                'title' => 'Road Safety & Public Safety', 'slug' => 'road-safety-and-public-safety', 'icon' => 'cone-striped',
                'excerpt' => 'Road safety awareness, traffic education, emergency response, first-aid training and accident prevention.',
                'body' => [
                    'Road accidents take far too many lives. We run road-safety and traffic-education sessions in schools and colleges, promote helmet and seat-belt use, and spread awareness on safe driving.',
                    'We train volunteers in first aid and emergency response so that accident victims get help in the critical first minutes.',
                ],
                'highlights' => ['Traffic education in schools and colleges', 'Helmet and seat-belt awareness drives', 'First-aid and emergency-response training', 'Accident-prevention campaigns'],
            ],
            [
                'title' => 'Disaster Relief', 'slug' => 'disaster-relief', 'icon' => 'life-preserver',
                'excerpt' => 'Preparedness, relief, humanitarian assistance and rescue support during natural or man-made calamities.',
                'body' => [
                    'Floods, cold waves and other calamities hit the poorest the hardest. We help communities prepare in advance and respond quickly when disaster strikes — with food, water, clothing, shelter material and medical aid.',
                    'Our volunteers coordinate with local authorities to support rescue and rehabilitation efforts.',
                ],
                'highlights' => ['Community disaster-preparedness training', 'Emergency relief kits and supplies', 'Support for rescue and rehabilitation', 'Coordination with district administration'],
            ],
            [
                'title' => 'Agriculture', 'slug' => 'agriculture', 'icon' => 'flower1',
                'excerpt' => 'Sustainable farming, agricultural awareness and conservation of natural resources for farming families.',
                'body' => [
                    'Agriculture is the backbone of Gonda. We help farming families learn about sustainable and natural farming methods, soil health, water-efficient irrigation and relevant government schemes.',
                    'By conserving natural resources, we aim to make farming more productive today and sustainable for the next generation.',
                ],
                'highlights' => ['Sustainable and natural farming awareness', 'Soil health and water-efficient irrigation', 'Information on government schemes for farmers', 'Natural resource conservation'],
            ],
            [
                'title' => 'Sports', 'slug' => 'sports', 'icon' => 'trophy',
                'excerpt' => 'Physical education, fitness, sports training, tournaments and talent development for young people.',
                'body' => [
                    'Sport builds health, discipline and teamwork. We promote physical education and fitness, organise sports training and tournaments, and help talented young athletes from modest backgrounds get the support they need.',
                ],
                'highlights' => ['Sports training and coaching camps', 'Local tournaments and sports events', 'Fitness and yoga sessions', 'Support for talented young athletes'],
            ],
            [
                'title' => 'Arts, Culture & Heritage', 'slug' => 'arts-culture-and-heritage', 'icon' => 'palette',
                'excerpt' => 'Literature, music, handicrafts, traditional knowledge, festivals, exhibitions and awards.',
                'body' => [
                    'Our culture is our identity. We celebrate and preserve literature, music, handicrafts and traditional knowledge through festivals, exhibitions, competitions and awards.',
                    'Local artisans get a platform to showcase their work, and young people get a chance to discover their creative talents.',
                ],
                'highlights' => ['Cultural festivals and celebrations', 'Exhibitions for local artisans and handicrafts', 'Art, music and literature competitions', 'Preservation of traditional knowledge'],
            ],
            [
                'title' => 'Partnerships', 'slug' => 'partnerships', 'icon' => 'people',
                'excerpt' => 'Collaboration with government bodies, educational institutions, trusts, companies and NGOs for greater impact.',
                'body' => [
                    'No organisation can change the world alone. We collaborate with government departments, educational institutions, trusts, companies and fellow NGOs to implement programmes, share resources and reach more people.',
                    'Companies can partner with us on CSR projects with clear objectives, transparent reporting and visible on-ground impact.',
                ],
                'highlights' => ['CSR project implementation', 'Government scheme outreach', 'Partnerships with schools and colleges', 'Collaboration with trusts and NGOs'],
            ],
        ];
    }
}
