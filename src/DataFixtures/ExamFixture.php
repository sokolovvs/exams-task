<?php

namespace App\DataFixtures;

use App\Entity\Exam;
use App\Entity\Option;
use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ExamFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist(self::exam());
        $manager->flush();
    }

    public static function exam(): Exam
    {
        return new Exam(
            'Math test',
            new Question(
                '1 + 1 =',
                new Option('3', false),
                new Option('2', true),
                new Option('0', false),
            ),
            new Question(
                '2 + 2 =',
                new Option('4', true),
                new Option('3 + 1', true),
                new Option('10', false),
            ),
            new Question(
                '3 + 3 =',
                new Option('1 + 5', true),
                new Option('1', false),
                new Option('6', true),
                new Option('2 + 4', true),
            ),
            new Question(
                '4 + 4 =',
                new Option('8', true),
                new Option('4', false),
                new Option('0', false),
                new Option('0 + 8', true),
            ),
            new Question('5 + 5 =',
                new Option('6', false),
                new Option('18', false),
                new Option('10', true),
                new Option('9', false),
                new Option('0', false),
            ),
            new Question('6 + 6 =',
                new Option('3', false),
                new Option('9', false),
                new Option('0', false),
                new Option('12', true),
                new Option('5 + 7', true),
            ),
            new Question('7 + 7 =',
                new Option('5', false),
                new Option('14', true),
            ),
            new Question('8 + 8 =',
                new Option('16', true),
                new Option('12', false),
                new Option('9', false),
                new Option('5', false),
            ),
            new Question('9 + 9 =',
                new Option('18', true),
                new Option('9', false),
                new Option('17 + 1', true),
                new Option('2 + 16', true),
            ),
            new Question('10 + 10 =',
                new Option('0', false),
                new Option('2', false),
                new Option('8', false),
                new Option('20', true),
            ),
        );
    }
}