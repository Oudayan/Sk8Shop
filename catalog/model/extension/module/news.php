<?php
/**
 * @file            news.php
 * @description     Model for news module
 * @author          Mathieu Sylvestre et Oudayan Dutta
 * @last modified   2018-01-19
 */

class ModelExtensionModuleNews extends Model {
    /**
     * @brief   Creates news table in database
     * @detail  Creates news table in database and populates it with 2 entries.
     */
    public function createNewsTable() {
    $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "news (
        `news_id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(255) NOT NULL,
        `description` text NOT NULL,
        `date_added` datetime NOT NULL,
        PRIMARY KEY (`news_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

    $this->db->query("INSERT INTO " . DB_PREFIX . "news (`news_id`, `title`, `description`, `date_added`) VALUES
        (1, 'Nouvelle 1', '<p>Maecenas accumsan lacus vel facilisis volutpat est velit egestas. Tincidunt vitae semper quis lectus. Egestas purus viverra accumsan in nisl. Id aliquet risus feugiat in ante metus dictum at. Pharetra vel turpis nunc eget lorem dolor sed viverra ipsum. Arcu ac tortor dignissim convallis aenean et. Faucibus in ornare quam viverra orci sagittis. Vel eros donec ac odio. Condimentum vitae sapien pellentesque habitant morbi tristique senectus et. Leo duis ut diam quam nulla.</p><p>Pharetra massa massa ultricies mi quis hendrerit. Viverra vitae congue eu consequat ac felis. Adipiscing enim eu turpis egestas. Posuere urna nec tincidunt praesent semper. Ut eu sem integer vitae justo eget magna fermentum. Id consectetur purus ut faucibus pulvinar elementum integer enim. Nisi porta lorem mollis aliquam. Dui ut ornare lectus sit amet. Egestas diam in arcu cursus euismod quis. Elementum tempus egestas sed sed risus. Mi bibendum neque egestas congue. Sit amet est placerat in egestas erat imperdiet sed. Platea dictumst vestibulum rhoncus est pellentesque elit ullamcorper dignissim.</p>', '2018-01-10 09:00:00'),
        (2, 'Nouvelle 2', 'Gravida in fermentum et sollicitudin ac orci. Cursus sit amet dictum sit amet justo donec enim diam. Neque ornare aenean euismod elementum nisi quis eleifend quam. Vestibulum mattis ullamcorper velit sed ullamcorper morbi tincidunt. Nibh sed pulvinar proin gravida hendrerit lectus. Pellentesque massa placerat duis ultricies lacus sed turpis tincidunt. Elementum integer enim neque volutpat ac tincidunt. Tortor vitae purus faucibus ornare suspendisse sed nisi lacus. Amet consectetur adipiscing elit ut aliquam. Aenean vel elit scelerisque mauris pellentesque pulvinar pellentesque. Non tellus orci ac auctor. Aliquam eleifend mi in nulla posuere. Tristique risus nec feugiat in fermentum posuere. Arcu odio ut sem nulla pharetra. Nec ultrices dui sapien eget mi proin sed.', '2018-01-11 08:30:00');");
    }

    /**
     * @brief   Gets all fields of a news from the news table
     * @param   [int] unique identifier of the news
     * @return  [array] all fields from a news
     */
    public function getNews($news_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news WHERE news_id = '" . $news_id . "'");
        if ($query->num_rows) {
            return $query->row;
        }
        else {
            return false;
        }
    }

    /**
     * @brief   Gets all fields of all news from the news table, by descending order of the date
     * @return  [array] all fields from all news
     */
    public function getAllNews() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "news ORDER BY date_added DESC");
        return $query->rows;
    }
}

?>