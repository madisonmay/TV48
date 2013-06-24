
<?
	class Post extends AppModel {
		public $validate = array(
		    'title' => array(
		        'rule' => 'notEmpty',
		        'message' => 'Your blog definitely needs a title.'
		    ),
		    'body' => array(
		        'rule' => 'notEmpty',
		        'message' => 'Please actually blog something.'
		    )
		);
	}
?>