<?php

namespace Clive0417\DBDiagramRegularParse\Creators;

use Clive0417\DBDiagramRegularParse\Models\Models\NameSpaceModel;
use Illuminate\Support\Facades\File;

class ModelCreator
{
    protected $entity_path;

    protected $entity_name;
    protected $namespace;

    protected $stub_path = __DIR__ . '/../stubs/Models/model.stub';


    protected $stub;

    protected $class_name;

    protected $use = [];
    protected $traits = [];
    protected $table;
    protected $fillables;
    protected $hidden;
    protected $dates;
    protected $setter_getters = [];
    protected $has_one = [];
    protected $has_many = [];
    protected $belongs_to = [];
    protected $belongs_to_many = [];


    public function __construct()
    {
        $this->stub = File::get($this->stub_path);

    }

    public function replaceDummyWordsInStub()
    {
        // 替換 namespace
        $this->stub = str_replace('{{namespace}}', $this->getNamespace()->toLine(), $this->stub);

        // 替換 use
        if (!empty($this->getUse())) {
            $use_full_test = '';
            foreach ($this->getUse() as $Use) {
                /** @var \Clive0417\ModelGenerator\Models\UseModel $Use */
                $use_full_test = $use_full_test . $Use->toLine() . PHP_EOL;
            }
            $this->stub = str_replace('{{use}}', $use_full_test, $this->stub);
        } else {
            $this->stub = str_replace('{{use}}', '', $this->stub);
        }
        //替換 class
        $this->stub = str_replace('{{class}}', $this->getClassName()->toLine(), $this->stub);

        //替換 trait
        if (!empty($this->getTraits())) {
            $trait_full_test = '';
            foreach ($this->getTraits() as $Trait) {
                /** @var \Clive0417\ModelGenerator\Models\TraitModel $Trait */
                $trait_full_test = $trait_full_test . $Trait->toLine() . PHP_EOL;
            }
            $this->stub = str_replace('{{trait}}', $trait_full_test, $this->stub);
        } else {
            $this->stub = str_replace('{{trait}}', '', $this->stub);
        }

        //替換 table
        $this->stub = str_replace('{{table}}', $this->getTable()->toLine(), $this->stub);

        //替換 fillable
        $this->stub = str_replace('{{fillable}}', $this->getFillables()->toLine(), $this->stub);

        //替換 hidden
        $this->stub = str_replace('{{hidden}}', $this->getHidden()->toLine(), $this->stub);

        //替換 dates
        $this->stub = str_replace('{{dates}}', $this->getDates()->toLine(), $this->stub);

        //替換 setter_getter
        $setter_getter_full_test = '';
        foreach ($this->getSetterGetters() as $SetterGetter) {
            /** @var \Clive0417\ModelGenerator\Models\SetterGetterModel $SetterGetter */
            $setter_getter_full_test = $setter_getter_full_test . $SetterGetter->toLine() . PHP_EOL;
        }
        $this->stub = str_replace('{{setter_getter}}', $setter_getter_full_test, $this->stub,);

        //替換 HasOne relation
        $hasOne_full_text = '';
        foreach ($this->getHasOne() as $has_one) {
            $hasOne_full_text = '    ' . $hasOne_full_text . PHP_EOL . '    ' . $has_one[1] . PHP_EOL;
        }
        $this->stub = str_replace('{{hasOne}}', $hasOne_full_text, $this->stub);


        //替換 HasMany relation
        $hasMany_full_text   = '';
        foreach ($this->getHasMany() as $has_many) {
            $hasMany_full_text = '    ' . $hasMany_full_text . PHP_EOL . '    ' . $has_many[1] . PHP_EOL;
        }
        $this->stub = str_replace('{{hasMany}}', $hasMany_full_text, $this->stub);

        //替換 belongsTo relation
        $belongsTo_full_text   = '';
        foreach ($this->getBelongsTo() as $belongs_to) {
            $belongsTo_full_text = '    ' . $belongsTo_full_text . PHP_EOL . '    ' . $belongs_to[1] . PHP_EOL;
        }
        $this->stub = str_replace('{{belongsTo}}', $belongsTo_full_text, $this->stub);


        //替換 belongsToMany relation
        $belongsToMany_full_text   = '';
        foreach ($this->getBelongsToMany() as $belongs_to_many) {
            $belongsToMany_full_text = '    ' . $belongsToMany_full_text . PHP_EOL . '    ' . $belongs_to_many[1] . PHP_EOL;
        }
        $this->stub = str_replace('{{belongsToMany}}', $belongsToMany_full_text, $this->stub);

        return $this;
    }

    public function outputEntity()
    {
        if (!File::exists($this->getEntityPath()->toLine())) {
            File::makeDirectory($this->getEntityPath()->toLine(), $mode = 0777, true, true);
        }

        File::put($this->getEntityPath()->toLine() . $this->getEntityName()->toLine() . '.php', $this->getStub());
    }

    public function addUse($Use)
    {
        $this->use[] = $Use;
        return $this;
    }

    /**
     * @Author  : Shou
     * @DateTime:2022/9/11 9:00 上午
     */
    public function addTrait($Trait)
    {
        $this->traits[] = $Trait;
        return $this;
    }


    public function addSetterGetter($SetterGetter)
    {
        $this->setter_getters[] = $SetterGetter;
        return $this;
    }

    public function addHasOne(array $has_one)
    {
        $this->has_one[] = $has_one;
        return $this;
    }


    public function addHasMany(array $has_many)
    {
        $this->has_many[] = $has_many;
        return $this;
    }

    public function addBelongsTo(array $belongs_to)
    {
        $this->belongs_to[] = $belongs_to;
        return $this;
    }

    public function addBelongsToMany(array $belongs_to_many)
    {
        $this->belongs_to_many[] = $belongs_to_many;
        return $this;

    }


    public function setEntityPath($entity_path)
    {
        $this->entity_path = $entity_path;
        return $this;
    }


    public function getEntityPath()
    {
        return $this->entity_path;
    }


    public function setEntityName($entity_name)
    {
        $this->entity_name = $entity_name;
        return $this;
    }


    public function getEntityName()
    {
        return $this->entity_name;
    }


    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getNamespace(): NameSpaceModel
    {
        return $this->namespace;
    }


    public function setStubPath(string $stub_path)
    {
        $this->stub_path = $stub_path;
        return $this;
    }

    /**
     * @return string
     */
    public function getStubPath(): string
    {
        return $this->stub_path;
    }


    public function setStub(string $stub)
    {
        $this->stub = $stub;
        return $this;
    }

    /**
     * @return string
     */
    public function getStub(): string
    {
        return $this->stub;
    }


    public function setClassName($class_name)
    {
        $this->class_name = $class_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->class_name;
    }


    public function setUse(array $use)
    {
        $this->use = $use;
        return $this;
    }

    /**
     * @return array
     */
    public function getUse(): array
    {
        return $this->use;
    }

    /**
     * @param array $traits
     */
    public function setTraits(array $traits)
    {
        $this->traits = $traits;
        return $this;
    }

    /**
     * @return array
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * @param mixed $table
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $fillables
     */
    public function setFillables($fillables)
    {
        $this->fillables = $fillables;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFillables()
    {
        return $this->fillables;
    }

    /**
     * @param mixed $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * @param mixed $dates
     */
    public function setDates($dates)
    {
        $this->dates = $dates;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * @param array $setter_getters
     */
    public function setSetterGetters(array $setter_getters)
    {
        $this->setter_getters = $setter_getters;
        return $this;
    }

    /**
     * @return array
     */
    public function getSetterGetters(): array
    {
        return $this->setter_getters;
    }

    public function getHasOne(): array
    {
        return $this->has_one;
    }


    public function getHasMany(): array
    {
        return $this->has_many;
    }

    public function getBelongsTo(): array
    {
        return $this->belongs_to;
    }

    public function getBelongsToMany(): array
    {
        return $this->belongs_to_many;
    }

}
