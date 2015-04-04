<?php

namespace Application\Model;

class Shorten {

 public $id, $short, $link, $views, $active, $description, $last_accessed, $created_at;

 public function exchangeArray($data) {

  $this->id = (isset($data['id'])) ? $data['id'] : null;
  $this->short = (isset($data['short'])) ? $data['short'] : null;
  $this->link = (isset($data['link'])) ? $data['link'] : null;
  $this->views = (isset($data['views'])) ? $data['views'] : null;
  $this->active = (isset($data['active'])) ? $data['active'] : null;
  $this->description = (isset($data['description'])) ? $data['description'] : null;
  $this->last_accessed = (isset($data['last_accessed'])) ? $data['last_accessed'] : null;
  $this->created_at = (isset($data['created_at'])) ? $data['created_at'] : null;
  
 }

}
