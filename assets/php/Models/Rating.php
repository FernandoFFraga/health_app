<?php

namespace App\Models;

use App\Core\Model;
use \PDO;

class Rating extends Model
{
    /** @var array */
    protected static $safe = ["id"];

    /** @var string */
    protected static $entity = "rating";

    public function bootstrap(string $name = "", string $phone = "(00) 00000-0000", string $text = "", int $stars = 1) : ?Rating
    {
        $session = new \Src\Core\Session();

        $this->name = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $name);
        $this->name = mb_convert_case(mb_substr($this->name, 0, 50, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
        
        $this->phone = $phone;
        $this->text = mb_substr($text, 0, 120);
        $this->text  = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $this->text);
        $this->stars = $stars;
        $this->session = $session->id();
        $this->time = date("dmY");
        return $this;
    }

    public function find(string $session, string $time, string $columns = "*") : ?Rating
    {
        $find = $this->read(
            "SELECT {$columns} FROM ". self::$entity ." WHERE session = :session AND time = :time",
            "session={$session}&time={$time}"
        );
        if ($this->fail || !$find->rowCount()) {
            $this->message = "Nenhuma avaliação encontrada";
            return null;
        }

        return $find->fetchObject(__CLASS__);
    }

    public function load(int $id, string $columns = "*")
    {
        $load = $this->read(
            "SELECT {$columns} FROM ". self::$entity ." WHERE id = :id",
            "id={$id}"
        );
        if ($this->fail || !$load->rowCount()) {
            $this->message = "Nenhuma avaliação encontrada!";
            return null;
        }
        return $load->fetchObject(__CLASS__);
    }

    public function all(int $limit = 30, string $columns = "*", string $mode = "hidden") : ?array
    {
        $all = $this->read(
            "SELECT {$columns} FROM ". self::$entity ." WHERE visibility = :visibility ORDER BY id LIMIT :limit",
            "visibility={$mode}&limit={$limit}"
        );
        
        if ($this->fail || !$all->rowCount()) {
            $this->message = "Nenhuma avaliação encontrada!";
            return null;
        }

        return $all->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    public function page(int $page = 1, string $columns = "*", int $reg = 16) : ?array
    {
        $page--;
        $offset = $page * $reg;

        $count = $this->read(
            "SELECT {$columns} FROM ".self::$entity." WHERE visibility = 'visible'"
        );

        $list = $this->read(
            "SELECT {$columns} FROM ".self::$entity." WHERE visibility = 'visible'
            ORDER BY id DESC LIMIT {$offset},{$reg}"
        );

        $count = $count->rowCount();
        $return = $list->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        $return['reg'] = $count;

        if($count > 0){
            $pages = intdiv($count, $reg);
            $pages = ($count % $reg > 0) ? $pages + 1 : $pages;
            $return['pages'] =  $pages;
        }
        return  $return;
    }

    public function save(): ?Rating
    {
        if (!$this->required()) {
            return null;
        }

        /** Update */
        if (!empty($this->id)) {
            $rating = $this->id;
            $this->update(self::$entity, $this->safe(), "id = :id", "id={$rating}");

            if ($this->fail()) {
                $this->message = "Erro ao atualizar!";
            }

            $this->message = "Dados atualizados com sucesso!";
        }

        /** Create */
        if (empty($this->id)) {

            $infos = $this->safe();
            $rating = $this->create(self::$entity, $infos);

            if ($this->fail()) {
                $this->message = "Erro ao cadastrar!";
            }
        }

        $this->data = $this->read(
            "SELECT * FROM ". self::$entity ." WHERE id = :id",
            "id={$rating}"
        )->fetchObject();

        return $this;
    }

    public function average(){
        $average = $this->read("SELECT count(id) as ratings, avg(stars) as average, max(stars) as maximum, min(stars) as minimun FROM ".self::$entity);
        return $average->fetch(PDO::FETCH_OBJ);
    }

    /**
     * @return boolean
     */
    private function required() : bool
    {   
        if (empty($this->name) || empty($this->phone) || empty($this->text) || empty($this->stars)) {
            $this->message = "Preencha os campos obrigatórios!";
            return false;
        }
        return true;
    }

    public function accept() :?Rating
    {
        $this->visibility = "visible";
        if(!$this->save()){
            return null;
        }

        return $this;
    }

    public function head(int $limit = 4, string $columns = "*")
    {
        $head = $this->read(
            "SELECT {$columns} FROM ".self::$entity." WHERE visibility = 'visible' ORDER BY RAND() LIMIT :limit",
            "limit={$limit}"
        );

        if ($this->fail()) {
            $this->message = "Não foi possível deletar!";
            return null;
        }

        return $head->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    public function destroy() : ?Rating
    {
        if (!empty($this->id)) {
            $this->delete(self::$entity, "id = :id", "id={$this->id}");
        }

        if ($this->fail()) {
            $this->message = "Não foi possível deletar!";
            return null;
        }

        $this->message = "Endereço deletado com sucesso!";
        $this->data = null;
        return $this;
    }
}
