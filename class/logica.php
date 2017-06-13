<?php

require_once('datos.php');

class LogicaPDO
{

	static function isPDO($arg = null){
		return ($arg instanceof PDO);
	}

	static function basicEval($query,$fields,$pdo){		

		return ( is_array($fields) || is_null($fields) ) && self::isPDO($pdo);
	}

	static function _fetch($query,$fields = null ,&$pdo=null){
    	$stmt       = false;
	    $pdo 		= (  self::isPDO($pdo) ) ?  $pdo : PDOConnection();
	    
	    if( self::basicEval($query,$fields,$pdo)   ){
	    
	    		try {
			        $stmt = $pdo->prepare($query);
			        $stmt->execute($fields);
			    } catch (PDOException $e) {
			    	self::gc($pdo,$query);
			        die($e->getMessage());
			    }
	    }

	    self::gc($pdo,$query);	// don't put $stmt 

	    return $stmt;
	}

	/**
	* Anti Injection Method
	*
	* @return array query result
	* @param Query string database query
	* @param fields array params
	* @param pdo object PDO connection
	**/
	static function fetchArr($Query,$fields = null ,&$pdo=null) {
		$rsp  = false;
		$stmt = self::_fetch($Query,$fields,$pdo);

		if($stmt){
			$rsp = $stmt->fetch(PDO::FETCH_ASSOC);
		}

		self::gc($pdo,$stmt);

	    return $rsp;
	}

	/**
	* Anti Injection Method
	*
	* @return object query result
	* @param Query string database query
	* @param fields array params
	* @param pdo object PDO connection
	**/
	static function fetchObj($Query,$fields = null ,&$pdo=null) {
	    $rsp  = false;
		$stmt = self::_fetch($Query,$fields,$pdo);

		if($stmt){
			$rsp = $stmt->fetchObject();
		}

		self::gc($pdo,$stmt);

	    return $rsp;
	}

	/**
	* Anti Injection Method
	*
	* @return Array Arrays query result
	* @param Query string database query
	* @param fields array params
	* @param pdo object PDO connection
	**/
	static function fetchAllArr($Query,$fields = null ,&$pdo=null) {
		$rsp  = false;
		$stmt = self::_fetch($Query,$fields,$pdo);
		if($stmt){
			$rsp = [];
		    while ($arr = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		        $rsp[] = $arr;
		    }
		}
		
		self::gc($pdo,$stmt);

	    return $rsp;
	}
	/**
	* Anti Injection Method
	*
	* @return Array Objects query result
	* @param Query string database query
	* @param fields array params
	* @param pdo object PDO connection
	**/
	static function fetchAllObj($Query,$fields = null ,&$pdo=null) {
		$rsp  = false;
		$stmt = self::_fetch($Query,$fields,$pdo);
		if($stmt){
			$rsp = [];
		    while ($obj = $stmt->fetchObject() ) {
		        $rsp[] = $obj;
		    }
		}
		
		self::gc($pdo,$stmt);

	    return $rsp;
	}
	
	static function parseDataFilter($data,&$pdo){
    	$values = [] ;

    	try {
    		foreach ($data as $key => $value) {
				//$names[] = (string) $key;
				
				$valor = $pdo->quote($value);
				$values[] = is_int($valor) ? $valor : "$key = :$key";
			}
    	} catch (Exception $e) {
    		self::gc($pdo,$stmt);
    	}

		return $values;
    }

	/**
	* Anti Injection Method UPDATE
	* @param $tabla string: Nombre de tabla
	* @param $data array: Columnas y valores a actualizar
	* @param $where array: Columnas y valores de filtro
	* @param pdo object PDO connection
	**/	
	
	static function update($tabla, array $data, array $where, &$pdo = null) {
    	$pdo 		= ( self::isPDO($pdo) ) ?  $pdo : PDOConnection();
		$whereArray = $setArray = array();
		$whereString = $setString = '';

		$tabla = (string) $tabla;
		$where = (array) $where;

		$rsp  = false;

		if (!empty($tabla) && !empty($data) && !empty($where)) {

			$setArray   = self::parseDataFilter($data, $pdo);
			$whereArray = self::parseDataFilter($where, $pdo);

			$setString   = implode(', ', $setArray);
			$whereString = implode(' AND ', $whereArray);
			
            $sql = "UPDATE $tabla SET $setString WHERE $whereString";
            $query = $pdo->prepare($sql);
			
			try {
				
				foreach ($data as $name => &$value) {
					$value = ($value==null) ?  "" : $value ;
					$query->bindParam( ":".$name, $value);
				}	
				foreach ($where as $name => &$value) {
					$value = ($value==null) ?  "" : $value ;
					$query->bindParam( ":".$name, $value);
				}

				$rsp = $query->execute();	
				
			} catch (PDOException $e) {
				self::gc($pdo,$query);
				die($e->getMessage());
			}   

		}

		self::gc($pdo,$query);

	    return $rsp;
    }

	/**
	* Anti Injection Method INSERT
	* @param $data array: Columnas y valores a guardar en la tabla
	* @param pdo object PDO connection
	**/	
		
	static function insert($tabla, array $data, &$pdo = null) {
    	$pdo   = ( self::isPDO($pdo) ) ?  $pdo : PDOConnection();

		$values = array();
		$query  = null;
		$tabla  = (string) $tabla;
		$data   = (array) $data;
		$return = array('success' => false, 'lastInsertId' => 0);


		if (!empty($tabla) && !empty($data)) {

			$values = self::parseDataFilter($data,$pdo);

			$valuesString = implode(', ', $values);
            
				$sql = "INSERT INTO $tabla SET $valuesString ";
				$query = $pdo->prepare($sql);

				try {
					
					foreach ($data as $name => &$value) {
						$value = ($value==null) ?  "" : $value ;
						$query->bindParam( ":".$name, $value);
					}	
					
					$query->execute();	
					$return['success'] = $query;
					$return['lastInsertId'] = $pdo->lastInsertId(); 

				} catch (PDOException $e) {
					self::gc($pdo,$query);
					die($e->getMessage());
					print "Error!: " . $e->getMessage() . "</br>"; 
				}   
		}

		self::gc($pdo,$query);

		return $return;
    }	


	/**
	* Anti Injection Method DELETE
	* @param $tabla string : nombre de la tabla
	* @param $data array: Columnas y valores para el where
	* @param pdo object PDO connection
	**/	
	static function delete($tabla, array $data, &$pdo = null) {

    	$pdo   = ( self::isPDO($pdo) ) ?  $pdo : PDOConnection();
		$names = $values = array();
		$tabla = (string) $tabla;
		$data   = (array) $data;
		$query  = null;
		$return = array('success' => false, 'lastInsertId' => 0);

		if (!empty($tabla) && !empty($data)) {
			
			$values = self::parseDataFilter($data,$pdo);

			$whereString = implode(' AND ', $values);
				
				$sql = "DELETE FROM $tabla WHERE $whereString ";
				$query = $pdo->prepare($sql);

				try {
					
					foreach ($data as $name => &$value) {
						$value = ($value==null) ?  "" : $value ;
						$query->bindParam( ":".$name, $value);
					}	
					
					$return = $query->execute();

				} catch (PDOException $e) {
					self::gc($pdo,$query);
					die($e->getMessage());
					print "Error!: " . $e->getMessage() . "</br>"; 
				}   
		}
		self::gc($pdo,$query);
		return $return;
    }		

	static function delet($tabla, array $data, $cn = null) {
    
		return self::delete($tabla,$data,$pdo);

    }	

	static function countrows($query, array $fields, &$pdo = null) {
      
		$rsp  = false;
		$stmt = self::_fetch($query,$fields,$pdo);
		try {
			
			$rows  = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$count = count( $rows );

		} catch (PDOException $e) {
			self::gc($pdo,$stmt);
			die($e->getMessage());
			print "Error!: " . $e->getMessage() . "</br>"; 
		}   

		self::gc($pdo,$stmt);

	    return $count;
    }	
	
	static function countcolumn($query, array $fields, &$pdo = null) {

		$stmt = self::_fetch($query,$fields,$pdo);

		try {
		
			$count = $stmt->columnCount();

		} catch (PDOException $e) {
			self::gc($pdo,$stmt);
			die($e->getMessage());
			print "Error!: " . $e->getMessage() . "</br>"; 
		}       

		$return[] = $count;
		$return[] = $stmt;  

		//Cerramos la conexión      
		self::gc($pdo,$stmt);
		
	    return $return;
    }	

	static function drop($tabla, &$pdo = null) {

		$pdo   = ( self::isPDO($pdo) ) ?  $pdo : PDOConnection();

		$sql = "DROP TABLE IF EXISTS $tabla  ";
		$query = $pdo->prepare($sql);
				
		try {
		
			$query->execute();
			$pdo = null;

		} catch (PDOException $e) {
			self::gc($pdo,$query);
			die($e->getMessage());
			print "Error!: " . $e->getMessage() . "</br>"; 
		}         
		self::gc($pdo,$query);

		$return[] = $count;
		$return[] = $stmt;  

	    return $return;
    }	

	static function ex($sql, &$pdo = null) {

		$pdo   = ( self::isPDO($pdo) ) ?  $pdo : PDOConnection();

		$query = $pdo->prepare($sql);
				
		try {
		
			$query->execute();

		} catch (PDOException $e) {
			self::gc($pdo,$query);
			die($e->getMessage());
			print "Error!: " . $e->getMessage() . "</br>"; 
		}     

		self::gc($pdo,$query);

		$return[] = $count;
		$return[] = $stmt;        
	    return $return;
    }	
	/** 
	* @param $connection PDO Connection
	* @param $statement PDO statement
	**/	
    static function gc(&$connection,&$statement){
    		$connection = null;
    		$statement  = null;
    }

}