<?php
/**
 * Auto generated by prado-cli.php on 2015-03-09 10:40:36.
 */
class QuerysBiRecord extends TActiveRecord
{
	const TABLE='querys_bi';

	public $codigo;
	public $descricao;
	public $query;
	public $colunas;
	public $tipo_grafico;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>