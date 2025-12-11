<?php
    include_once __DIR__. '/../Controllers/BaseController.php';
    include_once __DIR__. '/../Business/ImageManager.php';
    include_once __DIR__. '/../Models/ImageModel.php';

    class SearchController extends BaseController{
        private $imageManager;

        public function __construct(){
            $this->imageManager = new ImageManager();
        }

        public function index(){
            if(isset($_GET['q'])){
                $query = $_GET['q'];
                $results = $this->imageManager->searchImages($query);
                $data = ['results' => $results];
                // If called as AJAX (client sets ajax=1), return only the partial view without layout
                if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
                    extract($data);
                    include __DIR__ . '/../views/search_results_view.php';
                    return;
                }

                return $this->render('search_results_view', $data);
            } 
            return $this->render('search_view', ['results' => []]);
        }
    }
