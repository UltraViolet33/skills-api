<?php 




$userLevel = 0;



$skillLevel = 15.2;


$nextLevel = ceil($skillLevel);

echo "next level : ". $nextLevel . "\n";


$actionLevel = 2;



$skillLevel += $actionLevel * 0.2;
echo "skill level : " . $skillLevel . "\n";

if($skillLevel >= $nextLevel)
{
    $userLevel += 0.1;
}


echo "user level : " . $userLevel . "\n";









?>