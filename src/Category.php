<?php
class Category
{
    private $name;
    private $id;

    function __construct($name, $id = null)
    {
        $this->name = $name;
        $this->id = $id;
    }

    function setName($new_name)
    {
        $this->name = (string) $new_name;
    }

    function getName()
    {
        return $this->name;
    }

    function getId()
    {
        return $this->id;
    }

    function setId($new_id)
    {
        $this->id = $new_id;
    }

    function save()
    {
        $GLOBALS['DB']->exec("INSERT INTO categories (name) VALUES ('{$this->getName()}')");
        $result_id = $GLOBALS['DB']->lastInsertId();
        $this->setId($result_id);
    }

    static function getAll()
    {
        $returned_categories = $GLOBALS['DB']->query("SELECT * FROM categories;");
        $categories = array();
        foreach($returned_categories as $category) {
            $name = $category['name'];
            $id = $category['id'];
            $new_category = new Category($name, $id);
            array_push($categories, $new_category);
        }

        return $categories;
    }

    static function deleteAll()
    {
        $GLOBALS['DB']->exec("DELETE FROM categories;");
    }

    static function find($search_id)
    {
        $found_category = null;
        $categories = Category::getAll();
        foreach($categories as $category){
            $category_id = $category->getId();
            if ($category_id == $search_id){
                $found_category = $category;
            }
        }
        return $found_category;
    }

    function update($new_name)
    {
        $GLOBALS['DB']->exec("UPDATE categories SET name = '{$new_name}' WHERE id = {$this->getId()};");
        $this->setName($new_name);
    }

    function delete()
    {
        $GLOBALS['DB']->exec("DELETE FROM categories WHERE id = {$this->getId()};");
        $GLOBALS['DB']->exec("DELETE FROM categories_tasks WHERE category_id = {$this->getId()};");
    }

    function addTask($task)
    {
        $GLOBALS['DB']->exec("INSERT INTO categories_tasks (category_id, task_id) VALUES ({$this->getId()}, {$task->getId()});");
    }

    function getTasks()
    {
        $query = $GLOBALS['DB']->query("SELECT categories_tasks.task_id, tasks.due_date, tasks.description FROM categories_tasks INNER JOIN tasks ON categories_tasks.category_id = {$this->getId()} ORDER BY tasks.due_date ASC;");
        $task_ids = $query->fetchAll(PDO::FETCH_ASSOC);
        $tasks = Array();
        foreach($task_ids as $id) {
            // $task_id = $id['task_id'];
            // $result = $GLOBALS['DB']->query("SELECT * FROM tasks WHERE id = {$task_id};");
            // $returned_task = $result->fetchAll(PDO::FETCH_ASSOC);
            $description = $id['description'];
            $posid = $id['task_id'];
            $due_date = $id['due_date'];
            $new_task = new Task($description, $posid, $due_date);
            array_push($tasks, $new_task);
        }
        return $tasks;
    }
}



?>
