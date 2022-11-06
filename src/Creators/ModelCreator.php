<?php
namespace Clive0417\ModelGenerator\Supports;

use Clive0417\ModelGenerator\Models\NameSpaceModel;
use Illuminate\Support\Facades\File;

class ModelCreator
{
    protected $entity_path;

    protected $entity_name;
    protected $namespace;

    protected $stub_path = __DIR__ . '/../Commands/stubs/entity.stub';

    protected $stub;

    protected $class_name;

    protected $use = [];
    protected $traits =  [];
    protected $table;
    protected $fillables ;
    protected $hidden ;
    protected $dates ;
    protected $setter_getters = [];


    public function __construct()
    {
        $this->stub = File::get($this->stub_path);

    }

    public function addUse($Use)
    {
        $this->use[] = $Use;

    }

    /**
     * @Author  : Shou
     * @DateTime:2022/9/11 9:00 上午
     */
    public function addTrait($Trait)
    {
        $this->traits[] = $Trait;

    }


    public function addSetterGetter($SetterGetter)
    {
        $this->setter_getters[] = $SetterGetter;

    }

    /**
     * @param mixed $entity_path
     */
    public function setEntityPath($entity_path): void
    {
        $this->entity_path = $entity_path;
    }

    /**
     * @return mixed
     */
    public function getEntityPath()
    {
        return $this->entity_path;
    }

    /**
     * @param mixed $entity_name
     */
    public function setEntityName($entity_name): void
    {
        $this->entity_name = $entity_name;
    }

    /**
     * @return mixed
     */
    public function getEntityName()
    {
        return $this->entity_name;
    }


    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @return mixed
     */
    public function getNamespace(): NameSpaceModel
    {
        return $this->namespace;
    }

    /**
     * @param string $stub_path
     */
    public function setStubPath(string $stub_path): void
    {
        $this->stub_path = $stub_path;
    }

    /**
     * @return string
     */
    public function getStubPath(): string
    {
        return $this->stub_path;
    }

    /**
     * @param string $stub
     */
    public function setStub(string $stub): void
    {
        $this->stub = $stub;
    }

    /**
     * @return string
     */
    public function getStub(): string
    {
        return $this->stub;
    }

    /**
     * @param mixed $class_name
     */
    public function setClassName($class_name): void
    {
        $this->class_name = $class_name;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * @param array $use
     */
    public function setUse(array $use): void
    {
        $this->use = $use;
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
    public function setTraits(array $traits): void
    {
        $this->traits = $traits;
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
    public function setTable($table): void
    {
        $this->table = $table;
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
    public function setFillables($fillables): void
    {
        $this->fillables = $fillables;
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
    public function setHidden($hidden): void
    {
        $this->hidden = $hidden;
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
    public function setDates($dates): void
    {
        $this->dates = $dates;
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
    public function setSetterGetters(array $setter_getters): void
    {
        $this->setter_getters = $setter_getters;
    }

    /**
     * @return array
     */
    public function getSetterGetters(): array
    {
        return $this->setter_getters;
    }



    public function replaceDummyWordsInStub()
    {
        // 替換 namespace
        $this->stub = str_replace('{{namespace}}',$this->getNamespace()->toLine(),$this->stub);

        // 替換 use
        if (!empty($this->getUse())) {
            $use_full_test = '';
            foreach ($this->getUse() as $Use) {
                /** @var \Clive0417\ModelGenerator\Models\UseModel $Use */
                $use_full_test = $use_full_test.$Use->toLine().PHP_EOL;
            }
            $this->stub =  str_replace('{{use}}',$use_full_test,$this->stub);
        } else {
            $this->stub =  str_replace('{{use}}','',$this->stub);
        }
        //替換 class
        $this->stub =  str_replace('{{class}}',$this->getClassName()->toLine(),$this->stub);

        //替換 trait
        if (!empty($this->getTraits())) {
            $trait_full_test = '';
            foreach ($this->getTraits() as $Trait) {
                /** @var \Clive0417\ModelGenerator\Models\TraitModel $Trait */
                $trait_full_test = $trait_full_test.$Trait->toLine().PHP_EOL;
            }
            $this->stub =  str_replace('{{trait}}',$trait_full_test,$this->stub);
        } else {
            $this->stub =  str_replace('{{trait}}','',$this->stub);
        }

        //替換 table
        $this->stub =  str_replace('{{table}}',$this->getTable()->toLine(),$this->stub);

        //替換 fillable
        $this->stub =  str_replace('{{fillable}}',$this->getFillables()->toLine(),$this->stub);

        //替換 hidden
        $this->stub =  str_replace('{{hidden}}',$this->getHidden()->toLine(),$this->stub);

        //替換 dates
        $this->stub =  str_replace('{{dates}}',$this->getDates()->toLine(),$this->stub);

        //替換 setter_getter
        if (!empty($this->getSetterGetters())) {
            $setter_getter_full_test = '';
            foreach ($this->getSetterGetters() as $SetterGetter) {
                /** @var \Clive0417\ModelGenerator\Models\SetterGetterModel $SetterGetter */
                $setter_getter_full_test = $setter_getter_full_test.$SetterGetter->toLine().PHP_EOL;
            }
            $this->stub =  str_replace('{{setter_getter}}',$setter_getter_full_test,$this->stub,);
        }

        return $this;
    }

    public function outputEntity()
    {
        if (!File::exists($this->getEntityPath()->toLine())) {
            File::makeDirectory($this->getEntityPath()->toLine(), $mode = 0777, true, true);
        }

        File::put($this->getEntityPath()->toLine().$this->getEntityName()->toLine().'.php',$this->getStub());
    }
}
