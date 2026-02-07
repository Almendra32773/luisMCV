<?php
namespace Core;

use Exception;
use RedBeanPHP\R;

class Model
{
    protected static string $table;

    // Métodos CRUD básicos existentes
    public static function all(){
        return R::findAll(static::$table);
    }

    public static function find($id){
        return R::load(static::$table, $id);
    }

    public static function findByOne($column, $value){
        return R::findOne(static::$table, "$column = ?", [$value]);
    }

    public static function exists($column, $value){
        return R::findOne(static::$table, "$column = ?", [$value]) !== null;
    }

    public static function create(array $data){
        $bean = R::dispense(static::$table);

        foreach($data as $key => $value){
            $bean->$key = $value;
        }

        return R::store($bean);
    }

    public static function findOrFail($id)
    {
        $model = static::find($id);

        if ($model->id == 0) {
            throw new Exception('Recurso no encontrado', 404);
        }

        return $model;
    }

    // ============================================
    // MÉTODOS ADICIONALES PARA LOS MODELOS EXISTENTES
    // ============================================

    /**
     * Contar registros con condiciones
     */
    public static function count($conditions = '', $params = [])
    {
        return R::count(static::$table, $conditions, $params);
    }

    /**
     * Buscar con condiciones
     */
    public static function where($conditions = '', $params = [], $orderBy = '', $limit = null, $offset = null)
    {
        $query = $conditions;
        
        if ($orderBy) {
            $query .= " ORDER BY $orderBy";
        }
        
        if ($limit !== null) {
            $query .= " LIMIT " . intval($limit);
            if ($offset !== null) {
                $query .= " OFFSET " . intval($offset);
            }
        }
        
        return R::find(static::$table, $query, $params);
    }

    /**
     * Buscar todos con condiciones
     */
    public static function findAll($conditions = '', $params = [], $orderBy = '')
    {
        return static::where($conditions, $params, $orderBy);
    }

    /**
     * Actualizar registro
     */
    public static function update($id, array $data)
    {
        $bean = R::load(static::$table, $id);
        
        if ($bean->id == 0) {
            return false;
        }

        foreach ($data as $key => $value) {
            $bean->$key = $value;
        }

        return R::store($bean);
    }

    /**
     * Eliminar registro
     */
    public static function delete($id)
    {
        $bean = R::load(static::$table, $id);
        
        if ($bean->id == 0) {
            return false;
        }

        R::trash($bean);
        return true;
    }

    /**
     * Soft delete (marcar como inactivo)
     */
    public static function softDelete($id, $column = 'active')
    {
        return static::update($id, [$column => 0]);
    }

    /**
     * Ejecutar SQL personalizado (para consultas complejas)
     */
    public static function query($sql, $params = [])
    {
        return R::getAll($sql, $params);
    }

    /**
     * Ejecutar SQL y obtener una fila
     */
    public static function queryOne($sql, $params = [])
    {
        $result = R::getAll($sql, $params);
        return !empty($result) ? $result[0] : null;
    }

    /**
     * Ejecutar SQL de escritura
     */
    public static function execute($sql, $params = [])
    {
        return R::exec($sql, $params);
    }

    /**
     * Obtener último ID insertado
     */
    public static function lastInsertId()
    {
        return R::getInsertID();
    }

    /**
     * Buscar por múltiples columnas
     */
    public static function findBy($data)
    {
        $conditions = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $conditions[] = "$column = ?";
            $params[] = $value;
        }
        
        $where = implode(' AND ', $conditions);
        return R::findOne(static::$table, $where, $params);
    }

    /**
     * Crear o actualizar (upsert)
     */
    public static function updateOrCreate($where, $data)
    {
        $existing = static::findBy($where);
        
        if ($existing) {
            // Actualizar existente
            foreach ($data as $key => $value) {
                $existing->$key = $value;
            }
            return R::store($existing);
        } else {
            // Crear nuevo con ambos conjuntos de datos
            $allData = array_merge($where, $data);
            return static::create($allData);
        }
    }

    /**
     * Transacción
     */
    public static function transaction(callable $callback)
    {
        return R::transaction($callback);
    }

    /**
     * Incrementar campo numérico
     */
    public static function increment($id, $column, $amount = 1)
    {
        $sql = "UPDATE " . static::$table . " SET $column = $column + ? WHERE id = ?";
        return R::exec($sql, [$amount, $id]);
    }

    /**
     * Decrementar campo numérico
     */
    public static function decrement($id, $column, $amount = 1)
    {
        $sql = "UPDATE " . static::$table . " SET $column = $column - ? WHERE id = ?";
        return R::exec($sql, [$amount, $id]);
    }

    /**
     * Paginación simple
     */
    public static function paginate($conditions = '', $params = [], $page = 1, $perPage = 15, $orderBy = 'id DESC')
    {
        $offset = ($page - 1) * $perPage;
        
        $items = static::where($conditions, $params, $orderBy, $perPage, $offset);
        $total = static::count($conditions, $params);
        $totalPages = ceil($total / $perPage);
        
        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1
        ];
    }

    /**
     * Obtener todos ordenados
     */
    public static function allOrdered($orderBy = 'id DESC')
    {
        return R::findAll(static::$table, " ORDER BY $orderBy");
    }

    /**
     * Buscar con LIKE para búsquedas
     */
    public static function searchLike($column, $searchTerm, $conditions = '', $params = [], $orderBy = '', $limit = null)
    {
        $likeCondition = "$column LIKE ?";
        $likeParam = "%$searchTerm%";
        
        if ($conditions) {
            $where = "($likeCondition) AND ($conditions)";
            $allParams = array_merge([$likeParam], $params);
        } else {
            $where = $likeCondition;
            $allParams = [$likeParam];
        }
        
        return static::where($where, $allParams, $orderBy, $limit);
    }

    /**
     * Verificar si existe con múltiples condiciones
     */
    public static function existsWhere($conditions = '', $params = [])
    {
        return R::count(static::$table, $conditions, $params) > 0;
    }

    /**
     * Obtener valor de columna específica
     */
    public static function value($id, $column)
    {
        $bean = static::find($id);
        return $bean->id != 0 ? $bean->$column : null;
    }

    /**
     * Obtener array de valores de columna
     */
    public static function pluck($column, $conditions = '', $params = [])
    {
        $items = static::where($conditions, $params);
        $result = [];
        
        foreach ($items as $item) {
            $result[] = $item->$column;
        }
        
        return $result;
    }

    /**
     * Obtener máximo valor de columna
     */
    public static function max($column, $conditions = '', $params = [])
    {
        $sql = "SELECT MAX($column) as max_value FROM " . static::$table;
        
        if ($conditions) {
            $sql .= " WHERE $conditions";
        }
        
        $result = R::getRow($sql, $params);
        return $result['max_value'] ?? null;
    }

    /**
     * Obtener mínimo valor de columna
     */
    public static function min($column, $conditions = '', $params = [])
    {
        $sql = "SELECT MIN($column) as min_value FROM " . static::$table;
        
        if ($conditions) {
            $sql .= " WHERE $conditions";
        }
        
        $result = R::getRow($sql, $params);
        return $result['min_value'] ?? null;
    }

    /**
     * Obtener promedio de columna
     */
    public static function avg($column, $conditions = '', $params = [])
    {
        $sql = "SELECT AVG($column) as avg_value FROM " . static::$table;
        
        if ($conditions) {
            $sql .= " WHERE $conditions";
        }
        
        $result = R::getRow($sql, $params);
        return $result['avg_value'] ?? null;
    }

    /**
     * Sumar columna
     */
    public static function sum($column, $conditions = '', $params = [])
    {
        $sql = "SELECT SUM($column) as sum_value FROM " . static::$table;
        
        if ($conditions) {
            $sql .= " WHERE $conditions";
        }
        
        $result = R::getRow($sql, $params);
        return $result['sum_value'] ?? 0;
    }

    /**
     * Contar agrupado por columna
     */
    public static function countBy($column, $conditions = '', $params = [])
    {
        $sql = "SELECT $column, COUNT(*) as count FROM " . static::$table;
        
        if ($conditions) {
            $sql .= " WHERE $conditions";
        }
        
        $sql .= " GROUP BY $column";
        
        return R::getAll($sql, $params);
    }

    /**
     * Crear o encontrar (firstOrCreate)
     */
    public static function firstOrCreate($where, $data = [])
    {
        $existing = static::findBy($where);
        
        if ($existing) {
            return $existing;
        }
        
        $allData = array_merge($where, $data);
        return static::create($allData);
    }

    /**
     * Primero o crear nuevo (firstOrNew)
     */
    public static function firstOrNew($where, $data = [])
    {
        $existing = static::findBy($where);
        
        if ($existing) {
            return $existing;
        }
        
        $bean = R::dispense(static::$table);
        $allData = array_merge($where, $data);
        
        foreach ($allData as $key => $value) {
            $bean->$key = $value;
        }
        
        return $bean; // No guardado aún
    }

    /**
     * Obtener con relaciones de RedBean
     */
    public static function with($id, $relation)
    {
        $bean = static::find($id);
        
        if ($bean->id == 0) {
            return null;
        }
        
        return $bean->$relation;
    }
}

