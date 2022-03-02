<?php

class ModelProduct
{
    private $modelName;

    private $modelId;

    private $grades;

    public function __construct($modelId, $modelName,  $grades=null)
    {
        $this->modelName = $modelName;
        $this->modelId = $modelId;
        $this->grades = $grades;
    }

    // Methods
    public function setModelName($name)
    {
        $this->modelName = $name;
    }
    public function getModelName()
    {
        return $this->modelName;
    }


    public function setModelId($id)
    {
        $this->modelId = $id;
    }
    public function getModelId()
    {
        return $this->modelId;
    }

    public function setGrades($grades)
    {
        $this->grades = $grades;
    }
    public function getGrades()
    {
        return $this->grades;
    }
}

class GradeProduct
{
    private $gradeName;
    private $gradeId;

    public function __construct($gradeName, $gradeId)
    {
        $this->gradeName = $gradeName;
        $this->gradeId = $gradeId;
    }

    // Methods
    public function setGradeName($name)
    {
        $this->gradeName = $name;
    }
    public function getGradeName()
    {
        return $this->gradeName;
    }

    public function setGradeId($gradeId)
    {
        $this->gradeId = $gradeId;
    }
    public function getGradeId()
    {
        return $this->gradeId;
    }
}
