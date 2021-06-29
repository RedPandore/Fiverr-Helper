<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Quest;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Repository\QuestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ExperienceCalculator
{
    private const LEVEL_TABLE = [
        [
            1,
            150
        ],
        [
            2,
            160
        ],
        [
            3,
            170
        ],
        [
            4,
            180
        ],
        [
            5,
            200
        ],
        [
            6,
            210
        ],
        [
            7,
            220
        ],
        [
            8,
            240
        ],
        [
            9,
            250
        ],
        [
            10,
            260
        ],
        [
            11,
            280
        ],
        [
            12,
            300
        ],
        [
            13,
            310
        ],
        [
            14,
            330
        ],
        [
            15,
            350
        ],
        [
            16,
            360
        ],
        [
            17,
            380
        ],
        [
            18,
            400
        ],
        [
            19,
            420
        ],
        [
            20,
            440
        ],
        [
            21,
            460
        ],
        [
            22,
            480
        ],
        [
            23,
            500
        ],
        [
            24,
            520
        ],
        [
            25,
            550
        ],
        [
            26,
            570
        ],
        [
            27,
            590
        ],
        [
            28,
            620
        ],
        [
            29,
            640
        ],
        [
            30,
            660
        ],
        [
            31,
            690
        ],
        [
            32,
            720
        ],
        [
            33,
            740
        ],
        [
            34,
            770
        ],
        [
            35,
            800
        ],
        [
            36,
            820
        ],
        [
            37,
            850
        ],
        [
            38,
            880
        ],
        [
            39,
            910
        ],
        [
            40,
            940
        ],
        [
            41,
            970
        ],
        [
            42,
            1000
        ],
        [
            43,
            1030
        ],
        [
            44,
            1060
        ],
        [
            45,
            1100
        ],
        [
            46,
            1130
        ],
        [
            47,
            1160
        ],
        [
            48,
            1200
        ],
        [
            49,
            1230
        ],
        [
            50,
            1260
        ],
        [
            51,
            1300
        ],
        [
            52,
            1340
        ],
        [
            53,
            1370
        ],
        [
            54,
            1410
        ],
        [
            55,
            1450
        ],
        [
            56,
            1480
        ],
        [
            57,
            1520
        ],
        [
            58,
            1560
        ],
        [
            59,
            1600
        ],
        [
            60,
            1640
        ],
        [
            61,
            1680
        ],
        [
            62,
            1720
        ],
        [
            63,
            1760
        ],
        [
            64,
            1800
        ],
        [
            65,
            1850
        ],
        [
            66,
            1890
        ],
        [
            67,
            1930
        ],
        [
            68,
            1980
        ],
        [
            69,
            2020
        ],
        [
            70,
            2060
        ],
        [
            71,
            2110
        ],
        [
            72,
            2160
        ],
        [
            73,
            2200
        ],
        [
            74,
            2250
        ],
        [
            75,
            2300
        ],
        [
            76,
            2340
        ],
        [
            77,
            2390
        ],
        [
            78,
            2440
        ],
        [
            79,
            2490
        ],
        [
            80,
            2540
        ],
        [
            81,
            2590
        ],
        [
            82,
            2640
        ],
        [
            83,
            2690
        ],
        [
            84,
            2740
        ],
        [
            85,
            2800
        ],
        [
            86,
            2850
        ],
        [
            87,
            2900
        ],
        [
            88,
            2960
        ],
        [
            89,
            3010
        ],
        [
            90,
            3060
        ],
        [
            91,
            3120
        ],
        [
            92,
            3180
        ],
        [
            93,
            3230
        ],
        [
            94,
            3290
        ],
        [
            95,
            3350
        ],
        [
            96,
            3400
        ],
        [
            97,
            3460
        ],
        [
            98,
            3520
        ],
        [
            99,
            3580
        ],
        [
            100,
            3640
        ]
    ];

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    public function allLevel()
    {
        return self::LEVEL_TABLE;
    }

    public function percentageExperience()
    {
        if($this->user->getLevel() < 100){
       $userExperience = $this->user->getExperience();
       $neededExperience = self::LEVEL_TABLE[$this->user->getLevel()][1];

        return  $userExperience * 100 / $neededExperience;}
        return 100;
    }

    // Check if user have the minimum level to do quest
    public function isAvailable(Quest $quest)
    {
        if ($this->user->getLevel() >= $quest->getMinimumLevel()) {
            if ($this->user->getFinishedQuest()) {
            }
            return true;
        }
        return false;
    }

    // Simply add experience from quest to user
    public function addExperience(Quest $quest, EntityManagerInterface $em)
    {
        $this->user->setExperience($this->user->getExperience() + $quest->getExperience());
        $em->flush();
    }

    // Check if user have minimum Experience to reach next level
    public function canLevelUp(EntityManagerInterface $em)
    {
        /*
        * Your actual Step
        *   $actualStep = self::LEVEL_TABLE[$this->user->getLevel()-1];
        */

        // Verify if user is max level 
        if ($this->user->getLevel() < 100) {
            // Needed Experience to level up
            $neededExperience = self::LEVEL_TABLE[$this->user->getLevel()][1];

            if ($this->user->getExperience() >= $neededExperience) {
                $this->user->setLevel(self::LEVEL_TABLE[$this->user->getLevel()][0]);
                $em->flush();
            }
        }
    }

    // Save quest and user in user_quest table
    public function isAlreadyDo(Quest $quest, EntityManagerInterface $em)
    {
       $this->user->addFinishedQuest($quest);
       $em->flush();
    }
}
