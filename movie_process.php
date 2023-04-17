<?php 

    require_once("globals.php");
    require_once("db.php");
    require_once("models/Movie.php");
    require_once("dao/UserDAO.php");
    require_once("models/Message.php");
    require_once("dao/MovieDAO.php");

    $message = new Message($BASE_URL);
    $userDao = new UserDAO($conn, $BASE_URL);
    $movieDao = new MovieDAO($conn, $BASE_URL);

    $type = filter_input(INPUT_POST, "type");

    $userData = $userDao->verifyToken();

    if($type === "create") {

        $title = filter_input(INPUT_POST, "title");
        $description = filter_input(INPUT_POST, "description");
        $trailer = filter_input(INPUT_POST, "trailer");
        $category = filter_input(INPUT_POST, "category");
        $length = filter_input(INPUT_POST, "length");
      

        $movie = new Movie();

       

        if(!empty($title) && !empty($description) && !empty($category)) {

            $movie->title = $title;
            $movie->trailer = $trailer;
            $movie->description = $description;
            $movie->category = $category;
            $movie->length = $length;
            $movie->users_id = $userData->id;

            if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {
                $image = $_FILES["image"];
                $imageTypes = ["image/jpeg", "image/jpg", "image/png"];
                $jpgArray = ["image/jpeg", "image/jpg"];

                if(in_array($image["type"], $imageTypes)) {

                    if(in_array($image["type"], $jpgArray)){
                        $imageFile = imagecreatefromjpeg($image["tmp_name"]);
                        
                    } else{
                        $imageFile = imagecreatefrompng($image["tmp_name"]);
                    }   

                    $imageName = $movie->imageGenerateName();

                    imagejpeg($imageFile, "./imagens/movies/" . $imageName, 100);

                    $movie->image = $imageName;

                } else{
                    $message->setMessage("Tipo inválindo de imagem. Insira png, jpeg ou jpg", "error", "back");
                }
            }

            $movieDao->create($movie);

        } else{
            $message->setMessage("Você precisa adicionar Título, descrição e categoria.", "error", "back");
        }

    } else{

        $message->setMessage("Informações inválidas", "error", "index.php");
        
    }