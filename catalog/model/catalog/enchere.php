<?php
/**
 *@package	OpenCart
 *@file 	/catalog/model/catalog/enchere.php
 *@author 	Yuliya et Oudayan
 *@version 	1.0
 *@date 	16 décembre 2017
 *@brief 	Modèle du module Enchère côte client
 */

/**
* ModelCatalogEnchere class
*/
class ModelCatalogEnchere extends Model {
	
	/**
	 * @brief 	Fonction pour afficher un produit en enchères
	 * @detail 	Choisir l'enchère active (non-fini)
	 * @param 	(Int)   enchere_id
	 * @return	(Array) Toutes les colonnes de la table encheres
	*/	
	public function getProduitEnchere($enchere_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "encheres WHERE date_fin > NOW() AND enchere_id = " . $enchere_id);			
		return $query->row;		
	}
	
	/**
	 * @brief 	Fonction pour afficher le dernier prix maximum atteint
	 * @detail 	Retourne le data d'enchère avec le prix offert maximum 
	 * @param 	(Int)   enchere_id
	 * @return	(Array) Toutes les colonnes de la table historique_des_encheres
	*/	

	public function choosePrixMax ($enchere_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "historique_des_encheres WHERE enchere_id = '" . $enchere_id . "' AND prix_offert = (SELECT MAX(prix_offert) FROM " . DB_PREFIX . "historique_des_encheres AS he WHERE he.enchere_id = '" . $enchere_id . "')");
		return $query->row;
	}
	
	/**
	 * @brief 	Fonction pour créer un offre en historique_des_encheres
	 * @detail 	Ajoute un produit avec son prix offert par l'utilisateur et la date d'offre
	 * @param 	(Int)   enchere_id
	 * @return	(Int)   Identifiant unique de l'historique_des_encheres
	*/	
	public function addProductHistoriqueEncheres($enchere_id, $customer_id, $prix_offert) {
		$query = $this->db->query("INSERT into " . DB_PREFIX . "historique_des_encheres SET enchere_id = '" . $enchere_id . "',  customer_id = '" . $customer_id . "', prix_offert = '" . $this->db->escape($prix_offert) ."', date_offre = NOW()");
        // Va chercher le dernier id qui vient d'être inséré dans la table historique_des_encheres
		$historique_id = $this->db->getLastId();
        return $historique_id;
	}
}
?>