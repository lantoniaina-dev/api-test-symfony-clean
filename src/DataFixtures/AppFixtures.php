<?php

namespace App\DataFixtures;

// use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;
use Faker\Factory;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        // Creer 3 category fake
        for ($i = 0; $i <= 3; $i++) {
            $category = new Category();
            $category->setTitle("Category $i")
                ->setDescription($faker->sentence());

            $manager->persist($category);

            // Creer 6 articles fake
            for ($j = 0; $j <= 5; $j++) {
                $arcticle = new Article();

                $content = '' . join($faker->paragraphs(3)) . '';

                $arcticle->setTitle("Mon title $j")
                    ->setDescription("Description de mon article $j")
                    ->setContent($content)
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setImage("https://via.placeholder.com/350x150")
                    ->setCategory($category);

                $manager->persist($arcticle);

                for ($j = 0; $j <= 5; $j++) {
                    $comment = new Comment();

                    $content = '' . join($faker->paragraphs(2)) . '';

                    $now = new \DateTime();
                    $days = $now->diff($arcticle->getCreatedAt())->days; //date entre la creation et now 
                    $min = '-' . $days . 'days';  // -100 days

                    $comment->setAuthor($faker->name)
                        ->setContent($content)
                        ->setCreatedAt($faker->dateTimeBetween($min))
                        ->setArticle($arcticle);

                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
