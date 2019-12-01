<?php
namespace controllers;

use domain\author\AuthorRepositoryInterface;
use actionResults\ActionResultInterface;
use validation\Validator;
use authentication\UserAuthenticationServiceInterface;

class AuthorController extends AbstractController {
    private $_authorRepository;
    private $_userAuthenticationService;

    public function __construct(AuthorRepositoryInterface $authorRepository, UserAuthenticationServiceInterface $userAuthenticationService) {
        $this->_authorRepository = $authorRepository;
        $this->_userAuthenticationService = $userAuthenticationService;
    }

    public function Add(): ActionResultInterface {
        $returnUrl = $_GET["returnUrl"] ?? $_POST["returnUrl"] ?? "/";

        return parent::view("views/author/add.phtml", [
            "returnUrl" => $returnUrl
        ]);
    }
    
    public function AddPost(): ActionResultInterface {
        $formData = $_POST;
        $autorId = $formData["_authorId"];
        if(empty($autorId))
        {
            return parent ::view("views/author/add.phtml",[
                "errors" => ["Autor musí mít jméno"],
                "returnUrl" => "/"
                ]);
        }
        else
        {
            $autorId = $this->_authorRepository->GetAll();
            foreach($autorId as $existingAuthor){
                if($existingAuthor->getAuthor() == $autorId)
                    return parent::view("views/author/add.phtml", [ "errors" =>["Autor s tímto jménem již existuje"]] );}
        }
        $autorId = $this->_authorRepository->add($autorId);
        return parent::view("views/author/add.phtml");
    }
}