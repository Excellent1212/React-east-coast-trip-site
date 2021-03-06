<?php
    $routes = [
        "New York" =>
            [ 
                "Chicago" => 
                    [
                        "airplane" => ["costo"=>80,"durata"=>2]
                    ]
                ,
                "Los Angeles" => 
                    [
                        "bus" => ["costo"=>170,"durata"=>64],
                        "airplane" => ["costo"=>200,"durata"=>6]
                    ],
                "Philadelphia" => 
                    [
                        "bus" => ["costo"=>15,"durata"=>2],
                        "train" => ["costo"=>40,"durata"=>1]
                    ]
            ],
        "Philadelphia" =>
            [ 
                "New York" => 
                    [
                        "bus" => ["costo"=>15,"durata"=>2],
                        "train" => ["costo"=>40,"durata"=>1]
                    ]
                ,
                "Chicago" => 
                    [
                        "airplane" => ["costo"=>150,"durata"=>2]
                    ],
                "Washington" => 
                    [
                        "bus" => ["costo"=>25,"durata"=>3],
                        "train" => ["costo"=>45,"durata"=>2]
                    ]
            ],
        "Washington" =>
            [ 
                "Philadelphia" => 
                    [
                        "bus" => ["costo"=>25,"durata"=>3],
                        "train" => ["costo"=>45,"durata"=>2]
                    ]
                ,
                "Chicago" => 
                    [
                        "airplane" => ["costo"=>110,"durata"=>2]
                    ],
                "New Orleans" => 
                    [
                        "bus" => ["costo"=>30,"durata"=>100],
                        "train" => ["costo"=>160,"durata"=>26]
                    ]
            ],      
        "New Orleans" =>
            [ 
                "Washington" => 
                    [
                        "bus" => ["costo"=>30,"durata"=>100],
                        "train" => ["costo"=>160,"durata"=>26]
                    ],
                "Chicago" => 
                    [
                        "bus" => ["costo"=>21,"durata"=>80],
                        "airplane" => ["costo"=>80,"durata"=>2.5]
                    ],
                "Los Angeles" => 
                    [
                        "bus" => ["costo"=>150,"durata"=>36]
                    ]
            ],
        "Los Angeles" =>
            [ 
                "New York" => 
                    [
                        "bus" => ["costo"=>170,"durata"=>64],
                        "airplane" => ["costo"=>200,"durata"=>6]
                    ],
                "Chicago" => 
                    [
                        "bus" => ["costo"=>100,"durata"=>43],
                        "airplane" => ["costo"=>130,"durata"=>4]
                    ],
                "Seattle" => 
                    [
                        "bus" => ["costo"=>100,"durata"=>24],
                        "airplane" => ["costo"=>130,"durata"=>2.5]
                    ],
                "New Orleans" => 
                    [
                        "bus" => ["costo"=>150,"durata"=>36]
                    ]
            ],
        "Seattle" =>
            [ 
                "Chicago" => 
                    [
                        "bus" => ["costo"=>135,"durata"=>44],
                        "airplane" => ["costo"=>180,"durata"=>4]
                    ],
                "Los Angeles" => 
                    [
                        "bus" => ["costo"=>100,"durata"=>24],
                        "airplane" => ["costo"=>130,"durata"=>2.5]
                    ]
            ],
        "Chicago" =>
            [ 
                "New York" => 
                    [
                        "airplane" => ["costo"=>80,"durata"=>2]
                    ]
                ,
                "Philadelphia" => 
                    [
                        "airplane" => ["costo"=>150,"durata"=>2]
                    ],
                "Seattle" => 
                    [
                        "bus" => ["costo"=>135,"durata"=>44],
                        "airplane" => ["costo"=>180,"durata"=>4]
                    ],
                "New Orleans" => 
                    [
                        "bus" => ["costo"=>21,"durata"=>80],
                        "airplane" => ["costo"=>80,"durata"=>2.5]
                    ],
                "Los Angeles" => 
                    [
                        "bus" => ["costo"=>100,"durata"=>43],
                        "airplane" => ["costo"=>130,"durata"=>4]
                    ],
                "Washington" => 
                    [
                        "airplane" => ["costo"=>110,"durata"=>2]
                    ]
            ]
    ];

    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "c9";
    $table = "places";

    $connection = mysqli_connect($host, $user, $pass, $db);
    if (!$connection)
        die("Connection failed: " . mysqli_connect_error());

    $sql = "SELECT * FROM " . $table;

    $result = mysqli_query($connection, $sql);

    $resultAsJson = array();

$cityNames = array();

    if (mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $cityNames[$row["ID"]] = $row["city_name"];
        }
    }

    mysqli_close($connection);

    // $cityNames = [
    //     0 => "New York",
    //     1 => "Philadelphia",
    //     2 => "Chicago",
    //     3 => "Washington",
    //     4 => "New Orleans",
    //     5 => "Los Angeles",
    //     6 => "Seattle"
    // ];

    function getShortestPath ($startNode, $endNode, $routes, $cityNames, $isBus, $isPlane, $isTrain, $isCheap, $isFast) {
        $NEAREST_PATH = array();
        $LEFT_NODES = array();
        
        foreach (array_keys($routes) as $val)
            $LEFT_NODES[array_search($val, $cityNames)] = 99999;
        $LEFT_NODES[$startNode] = 0;
        
        // Starts calculating.
        while (!empty($LEFT_NODES)) {
            $lowestWeightIndex = array_search(min($LEFT_NODES), $LEFT_NODES);
            
            if ($lowestWeightIndex == $endNode)
                break;

            // key = nome città destinazione
            // val = array contenente i veicoli
            foreach ($routes[$cityNames[$lowestWeightIndex]] as $key => $val) {
            
                $vehicle = "";
                
                if ($isPlane && isset($val["airplane"]))
                    $vehicle = "airplane";
                else if ($isTrain && isset($val["train"]))
                    $vehicle = "train";
                else if ($isBus && isset($val["bus"]))
                    $vehicle = "bus";
                else
                    continue;
                
                if ($isCheap) {
                    $finalVal = $val[$vehicle]["costo"];
                }
                else if ($isFast) {
                    $finalVal = $val[$vehicle]["durata"];
                }
                
                $finalKey = array_search($key, $cityNames);
                
                if (!empty($LEFT_NODES[$finalKey]) && $LEFT_NODES[$lowestWeightIndex] + $finalVal < $LEFT_NODES[$finalKey]) {
                    $LEFT_NODES[$finalKey] = $LEFT_NODES[$lowestWeightIndex] + $finalVal;
                    $NEAREST_PATH[$finalKey] = array($lowestWeightIndex, $LEFT_NODES[$finalKey]);
                }
            }
            unset($LEFT_NODES[$lowestWeightIndex]);
        }
        
        // Builds result path.
        $path = array();
        $pos = $endNode;
        while ($pos != $startNode) {
            $path[] = $pos;
            $pos = $NEAREST_PATH[$pos][0];
        }
        $path[] = $startNode;
        $path = array_reverse($path);
        return $path;
    }
    
    $vehicle = "";
    
    $from = array_search($_GET["from"], $cityNames);
    $to = array_search($_GET["to"], $cityNames);
    
    $isBus = isset($_GET["bus"]);
    $isPlane = isset($_GET["airplane"]);
    $isTrain = isset($_GET["train"]);
    
    $isFast = false;
    $isCheap = false;
    
    if ($_GET["type"] == "fastest")
    $isFast = true;
    if ($_GET["type"] == "cheapest")
    $isCheap = true;
    if ($_GET["type"] == "most_scenic")
    $isCheap = true;
    
    
    function getAsJson ($result, $routes, $cityNames, $isBus, $isPlane, $isTrain, $isCheap, $isFast) {
        $jsonResult = array();    
        // TODO Fix this part as it now gives the wrong vehicle/time/price.
        for ($i = 0; $i < count($result) - 1; $i++) {
            if ($isPlane && isset($routes [$cityNames[$result[$i]]] [$cityNames[$result[$i+1]]] ["airplane"]))
            $vehicle = "airplane";
            else if ($isTrain && isset($routes [$cityNames[$result[$i]]] [$cityNames[$result[$i+1]]] ["train"]))
            $vehicle = "train";
            else if ($isBus && isset($routes [$cityNames[$result[$i]]] [$cityNames[$result[$i+1]]] ["bus"]))
            $vehicle = "bus";
            
            $jsonResult[$i]["from"] = $cityNames[$result[$i]];
            $jsonResult[$i]["to"] = $cityNames[$result[$i+1]];
            $jsonResult[$i]["fromID"] = $result[$i];
            $jsonResult[$i]["toID"] = $result[$i+1];
            $jsonResult[$i]["vehicle"] = $vehicle;
            $jsonResult[$i]["price"] = $routes [$cityNames[$result[$i]]] [$cityNames[$result[$i+1]]] [$vehicle] ["costo"];
            $jsonResult[$i]["time"] = $routes [$cityNames[$result[$i]]] [$cityNames[$result[$i+1]]] [$vehicle] ["durata"];
        }
        return $jsonResult;
    }

    $result = getShortestPath($from, $to, $routes, $cityNames, $isBus, $isPlane, $isTrain, $isCheap, $isFast);
    $result = getAsJson($result, $routes, $cityNames, $isBus, $isPlane, $isTrain, $isCheap, $isFast);
        
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Credentials: true ");
    header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
    header('Content-type: application/json');
    echo json_encode($result);
?>