<?php
/**
 *@package	OpenCart
 *@file 	/catalog/controller/extension/module/enchere.php
 *@author 	Yuliya et Oudayan
 *@version 	1.0
 *@date 	16 décembre 2017
 *@brief 	Controller du module Enchère côte client
 */

/**
* ControllerExtensionModuleEnchere class
*/
class ControllerExtensionModuleEnchere extends Controller {
	
	//tableau d'erreurs
	private $error = array();
	
	/**
	 * @brief 	Méthode index qui gère les interactions avec l'utilisateur
	 * @detail 	Détermine quels traitements doivent être effectués pour une action donnée
	 * @param 	setting (limit, width, height)
	 * @return	Vue 
 	*/	
    public function index($setting) {
        // Charger le texte de la vue enchere selon la langue
		$this->load->language('extension/module/enchere');
		
		//Chercher le titre
		$this->document->setTitle($this->language->get('heading_title'));
		
        // Charger le modèle enchere du côté catalogue
        $this->load->model('catalog/enchere');
		
        // Charger le modèle product du côté catalogue
		$this->load->model('catalog/product');
		
        // Charger le modèle image du côté catalogue
		$this->load->model('tool/image');
		
        // Création du tableau contenant les infos des enchères à passer à la vue enchere.twig du côté catalog
		$data['products'] = array();

        // Chercher la limite d'enchères à afficher dans la table module, colonne settings
		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}
		
        // Si des enchères sont présentes dans la table module, colonne settings
		if (!empty($setting['encheres_module'])) {
			
            // Garder dans le tableau $encheres le nombre d'enchères précisées dans limit de la table module, colonne settings
			$encheres = array_slice($setting['encheres_module'], 0, (int)$setting['limit']);
			
            // Boucler à travers les enchères du tableau $encheres
			foreach ($encheres as $enchere_id) {
				
				// Chercher les informations de l'enchère
                $enchere_info = $this->model_catalog_enchere->getProduitEnchere((int)$enchere_id);
				
				// Si l'info d'enchere existe
				if($enchere_info) {
					// Chercher la dernière offre avec le prix le plus élevé
					$prix_max = $this->model_catalog_enchere->choosePrixMax($enchere_id);
					
					// Chercher les informations du produit avec le product_id de la table encheres
					$product_info = $this->model_catalog_product->getProduct($enchere_info["product_id"]);
					
					// Si l'info du produit existe
					if ($product_info) {
						
						// Si le produit a une image
						if ($product_info['image']) {
							// Redimentionner l'image selon les width et height de la table module, colonne settings
							$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
						} 
						else {
							// Redimentionner l'image défault selon les width et height de la table module, colonne settings
							$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
						}
						
						if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
							$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						} else {
							$price = false;
						}

						if ((float)$product_info['special']) {
							$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						} else {
							$special = false;
						}

						if ($this->config->get('config_tax')) {
							$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
						} else {
							$tax = false;
						}

						if ($this->config->get('config_review_status')) {
							$rating = $product_info['rating'];
						} else {
							$rating = false;
						}
						
						// Aller chercher le prix de départ de l'enchère
						$prix_depart = $this->currency->format($enchere_info['prix_depart'], $this->session->data['currency']);

						// Si une offre a été faite sur l'enchère, aller chercher la dernière offre avec le prix le plus élevé
						if (isset($prix_max['prix_offert'])) {
							$prix_max = $this->currency->format($prix_max['prix_offert'], $this->session->data['currency']);
						} else {
							// Sinon, mettre le prix_max à la valeur de prix_depart.
							$prix_max = $prix_depart;
						}

						// Construire le tableau de données à passer à la vue enchere côté catalog
						$data['products'][] = array(
							'enchere_id'  => $enchere_info['enchere_id'],
							'date_debut'  => $enchere_info['date_debut'],					
							'date_fin'    => $enchere_info['date_fin'],
							'prix_depart' => $prix_depart,
							'prix_max'    => $prix_max,
							'product_id'  => $product_info['product_id'],
							'thumb'       => $image,
							'name'        => $product_info['name'],
							'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
							'price'       => $price,
							'special'     => $special,
							'tax'         => $tax,
							'rating'      => $rating,
							'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
						);
					}
				}
			}
		}
			
		//Vérifier le méthode post
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			
			//Vérifier si l'usager a envoyé le formulaire (submit)
			if(isset($this->request->post['btn_bid'])) {
				
				//Vérifier si le client est connecté
				if($this->customer->isLogged()) {
					
					//Obtenir l'id de client
					$customer_id = $this->customer->getId();
					
					//Si on a reçu l'id de l'enchere 
					if (isset($this->request->post['enchere_id'])) 
						$enchere_id_he = (int)$this->request->post['enchere_id'];
					
					//Si on a reçu le prix offert par le client
					if (isset($this->request->post['prix_input']) && !empty($this->request->post['prix_input'])) { 
						$prix_input = (int)$this->request->post['prix_input'];
						
						//On cherche le prix le plus élevé dans l'historique pour cette enchère						
						$prix_max = $this->model_catalog_enchere->choosePrixMax($enchere_id_he);
					
						//Récuperer l'info de cette enchère pour trouver le prix initial
						$enchere = $this->model_catalog_enchere->getProduitEnchere($enchere_id_he);
						
						//Si il n'y a pas encore des offres pour cette enchère, 
						if (!$prix_max)
							//le prix max offert est égal au prix initial
							$prix_max['prix_offert'] =  $enchere['prix_depart'];
						
						//Si le prix offert par le client est plus grand que le prix maximum dernier 
						//ou plus grand que le prix initial s'il n'y pas des offres
						if ($prix_input > $prix_max['prix_offert'] ) {
							//On créer l'entrée dans l'historique des enchères
							$ajout = $this->model_catalog_enchere->addProductHistoriqueEncheres($enchere_id_he, $customer_id, $prix_input);

							// Obtenir l'id de catégorie qu'affiche notre module
							$path = $this->request->get['path'];
							
							//Redirection vers la même page d'affichage du module après l'envoi du formulaire
							//$data['action'] = $this->response->redirect($this->url->link('product/category', 'path=' . $path, '', true));
							//$data['action'] = $this->response->redirect($this->url->link('extension/module/enchere', '', true));
    
                            //Redirection vers la même page d'affichage du module après l'envoi du formulaire 
                            if ($path) {
                                //si on vient du menu
                                $data['action'] = $this->response->redirect($this->url->link('product/category', 'path=' . $path, '', true));
                            } else {
                                //si on vient de la page d'accueil (module)
                                $data['action'] = $this->response->redirect($this->url->link('common/home', '', true));
                            }
						
						} else {
							//Afficher le message d'erreur si le prix offert est inférieur au prix existant
							$this->error['warning'] = $this->language->get('error_prixInferieur');
						}
					} else {
						////Afficher le message d'erreur si le client n'as pas rentré le prix
						$this->error['warning'] = $this->language->get('error_prixVide');
					}
				} else {
					//Afficher le message d'erreur si le client n'est pas connecté
					$this->error['warning'] = $this->language->get('error_authorisation');
				}
			}
		}
		
		//Vérifier l'existance des erreurs et setter les messages vides s'il n'y pas des erreurs 		
		if (isset($this->error['warning'])) {
			$data['error_authorisation'] = $this->error['warning'];
		} else {
			$data['error_authorisation'] = '';
		}
		
		if (isset($this->error['warning'])) {
			$data['error_prixInferieur'] = $this->error['warning'];
		} else {
			$data['error_prixInferieur'] = '';
		}
		
		if (isset($this->error['warning'])) {
			$data['error_prixVide'] = $this->error['warning'];
		} else {
			$data['error_prixVide'] = '';
		}
		
		// Appel de la vue
		return $this->load->view('extension/module/enchere', $data);
	}	
}