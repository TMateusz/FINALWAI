<?php
    include_once __DIR__. '/../Controllers/BaseController.php';

    class SearchController extends BaseController{
        private $imageManager;

        public function __construct(){
            $imageModel = new ImageModel();
            $this->imageManager = new ImageManager($imageModel);
        }

        public function index(){
            if(isset($_GET['q'])){
                $query = $_GET['q'];
                // pass current login to searchImages so private photos are visible to their owner
                $login = session_get('login', null);
                $results = $this->imageManager->searchImages($query, $login);
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
