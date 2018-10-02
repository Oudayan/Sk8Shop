<?php
/**
 * @file            news.php
 * @description     Contrôleur pour le module news
 * @author          Mathieu Sylvestre et Oudayan Dutta
 * @last modified   2018-01-19
 */

class ControllerExtensionModuleNews extends Controller {
	// Controller – Fichier du contrôleur.
	// module – Son répertoire.
	// News – nom du fichier news.php

	// Tous les contrôleur ont une fonction index()
	public function index() {
        // Charger le modèle news pour le côté catalog
        $this->load->model('extension/module/news');
        // Création des tables encheres et historique_des_encheres
        $this->model_extension_module_news->createNewsTable();
        // Charger le texte de la vue enchere pour le côté admin selon la langue 
		$this->load->language('extension/module/news'); // Appel au fichier langage
		// Titre de la page.
		$this->document->setTitle($this->language->get('heading_title'));
		// Fil d’Ariane
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home'),
			'separator' => false
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/news')
			// 'separator' => $this->language->get('text_separator')
		);

		// Texte pris dans le fichier du langage
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_title'] = $this->language->get('text_title');
		$data['text_description'] = $this->language->get('text_description');
		$data['text_view'] = $this->language->get('text_view');

		// Appel de la fonction getAllNews du modèle
		$all_news = $this->model_extension_module_news->getAllNews();
		$data['all_news'] = array();
		foreach ($all_news as $news) {
			$data['all_news'][] = array (
				'title' => $news['title'],
				'description' => (strlen(html_entity_decode($news['description'])) > 50 ? substr(strip_tags(html_entity_decode($news['description'])), 0, 50) . '..' : html_entity_decode($news['description'])),
				'view' => $this->url->link('extension/module/news/news', 'news_id=' . $news['news_id'])
			);
		}

		// Requis. Les fichiers contenus dans la page.
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		// Appel de la vue
		$this->response->setOutput($this->load->view('extension/module/news_list', $data));
	}

	// Fonction pour afficher le detail d’une news donnée par son ID
	public function news() {
		$this->load->model('extension/module/news');
		$this->load->language('extension/module/news');
		if (isset($this->request->get['news_id']) && !empty($this->request->get['news_id'])) {
			$news_id = $this->request->get['news_id'];
		} else {
			$news_id = 0;
		}

		$news = $this->model_extension_module_news->getNews($news_id);

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home'),
			'separator' => false
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/news'),
			'separator' => $this->language->get('text_separator')
		);

		if ($news) {
			$data['breadcrumbs'][] = array(
				'text' => $news['title'],
				'href' => $this->url->link('extension/module/news/news', 'news_id='.$news_id),
				'separator' => $this->language->get('text_separator')
			);

			$this->document->setTitle($news['title']);

			$data['heading_title'] = $news['title'];
			$data['description'] = html_entity_decode($news['description']);

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');

			$data['content_bottom'] = $this->load->controller('common/content_bottom');

			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			$this->response->setOutput($this->load->view('extension/module/news', $data));
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('extension/module/news', 'news_id='.$news_id),
				'separator' => $this->language->get('text_separator')
			);
		}
	}
}
?>