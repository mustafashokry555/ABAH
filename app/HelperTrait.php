<?php
function calculateAge($birthdate)
{
    // Create DateTime objects for birth date and current date
    $birthDate = new DateTime($birthdate);
    $currentDate = new DateTime();
    
    // Calculate the difference between the two dates
    $age = $currentDate->diff($birthDate);
    
    // Return the age in years
    return (string)$age->y;
}


