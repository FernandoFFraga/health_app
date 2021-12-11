<?php 

namespace App\Models;

use App\Core\Model;

class Day extends Model 
{
    /** @var array */
    protected static $safe = ["id"];

    /** @var string */
    protected static $entity = "days";

    public function bootstrap(array $params): ?Day {
        $this->water    = $params['water'];
        $this->training = $params['training'];
        $this->food     = $params['food'];
        $this->sleep    = $params['sleep'];
        $this->day      = $params['day'];

        return $this;
    }

    public function save(): ?Day
    {
        $search = $this->loadByDay($this->day);

        /** Update */
        if (!empty($search)) {
            $day = $search->id;
            $this->update(self::$entity, $this->safe(), "id = :id", "id={$day}");

            if ($this->fail()) {
                $this->message = "Erro ao atualizar!";
            }

            $this->message = "Dados atualizados com sucesso!";
        }

        /** Create */
        if (empty($search)) {

            $infos = $this->safe();
            $day = $this->create(self::$entity, $infos);

            if ($this->fail()) {
                $this->message = "Erro ao cadastrar!";
            }
        }

        $this->data = $this->read(
            "SELECT * FROM ". self::$entity ." WHERE id = :id",
            "id={$day}"
        )->fetchObject();

        return $this;
    }

    public function loadByDay(string $day, string $columns = "*")
    {
        $load = $this->read(
            "SELECT {$columns} FROM ". self::$entity ." WHERE day = :day",
            "day={$day}"
        );
        if ($this->fail || !$load->rowCount()) {
            $this->message = "Nenhuma data encontrada!";
            return null;
        }

        return $load->fetchObject(__CLASS__);
    }
}