<?php

return [

    new Route('/users','User','index', 'GET'),
    new Route('/get_users','User','getUsers', 'GET'),
    new Route('/get_relatives','User','getRelatives', 'GET'),
    new Route('/get_relatives_count','User','getRelativesCount', 'GET'),
    
    new Route('/get_users_csv','User','getUsersCSV', 'GET'),
];