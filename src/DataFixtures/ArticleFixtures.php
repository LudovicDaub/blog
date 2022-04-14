<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //création du faker
        $faker = Factory::create('fr_FR');

        //Création des catégories
        for ($i = 1; $i <= 3; $i++) {
            $category = new Category;
            $category
                ->setTitle($faker->sentence())
                ->setDescription($faker->paragraph());

            $manager->persist($category);
        }
        //Création des articles
        for ($j = 1; $j <= mt_rand(6, 9); $j++) {
            $article = new Article();

            $article
                ->setTitle($faker->sentence())
                ->setContent($faker->paragraph())
                ->setImage("https://picsum.photos/500/300")
                ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                ->setCategory($category);

            $manager->persist($article);
        }
        //Création des commentaires
        for ($k = 1; $k <= mt_rand(8, 15); $k++) {
            $comment = new Comment;

            //date de commentaire entre aujourd'hui et la création de l'article
            $days = (new \DateTime())->diff($article->getCreatedAt())->days;

            $comment
                ->setAuthor($faker->name())
                ->setContent($faker->paragraph())
                ->setCreatedAt($faker->dateTimeBetween('-' . $days . 'days'))
                ->setArticle($article);

            $manager->persist($comment);
        }

        $manager->flush();
    }
}
