<?php
  class  CategoryContainers {

    private $con; 
    private $username;
      
    public function __construct($con, $username) { 
      $this->con = $con;
      $this->username = $username;
    }

    public function showAllCategories() {  
      $query = $this->con->prepare("SELECT * FROM categories"); 
      $query->execute();

      $html = "<div class='previewCategories'>";

      while($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $html .=$this->getCategoryHtml($row, NULL, TRUE, TRUE);
      }
      return $html . "</div>";
    }
      
    public function showTVShowCategories(){
      $query = $this->con->prepare("SELECT * FROM categories"); 
      $query->execute();

      $html = "<div class='previewCategories'>
                <h1>TV Shows</h1>";
      while($row = $query->fetch(PDO::FETCH_ASSOC)){           
          $html .=$this->getCategoryHtml($row, NULL, TRUE, FALSE);
      }
      return $html . "</div>";
    }
      
    public function showMovieCategories(){    
      $query = $this->con->prepare("SELECT * FROM categories"); 
      $query->execute();

      $html = "<div class='previewCategories'>
                <h1>Movies</h1>";
      while($row = $query->fetch(PDO::FETCH_ASSOC)){
          $html .=$this->getCategoryHtml($row, NULL, FALSE, TRUE);
      }

      return $html . "</div>";
    }
  
    public function showCategory($categoryId, $title = null) {
      $query = $this->con->prepare("SELECT * FROM categories WHERE id=:id"); 
      $query->bindValue(":id", $categoryId);
      $query->execute();

      $html = "<div class='previewCategories noScroll'>";

      while($row = $query->fetch(PDO::FETCH_ASSOC)){  
          $html .=$this->getCategoryHtml($row, $title, TRUE, TRUE);
      }
      return $html . "</div>";
    }

    private function getCategoryHtml($sqlData, $title, $tvShows, $movies){
      $categoryId = $sqlData["id"]; 
      $title = $title == null ? $sqlData["name"] : $title;

      if($tvShows && $movies){
        $entities = EntityProvider::getEntities($this->con, $categoryId, 80);
      } else if ($tvShows){
        // GET TV show entities
        $entities = EntityProvider::getTVShowEntities($this->con, $categoryId, 80);
      } else { 
        // GET movies entities
        $entities = EntityProvider::getMovieEntities($this->con, $categoryId, 80);
      }

      if( sizeof($entities) == 0 ){
        return ;
      } 

      $entitiesHtml = "";
      
      $previewProvider = new PreviewProvider($this->con, $this->username); 
      
      foreach($entities as $entity){
        $entitiesHtml .= $previewProvider->createEntityPreviewSquare($entity);
      }         
          
      return "<div class='category'>
                <a href='category.php?id=$categoryId'>
                  <h3>$title<h3/> 
                </a>
                <div class='entities scrollbars_none'>
                  $entitiesHtml
                </div>
              </div> ";
    }
  }
?>  