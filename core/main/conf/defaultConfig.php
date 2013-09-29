<?php
return array(
				'Database' => array(
						'Driver' => 'mysql',
						'LoadBalancer' => 'localhost',
						'ImportNode' => 'localhost',
						'SecondaryNode' => 'localhost',
						'NASNode' => 'localhost',
						'CoreDatabase' => 'ezcashflow',
						'Username' => 'root',
						'Password' => 'root'
					),
				'Profiler' => array(
								'SQL' => false,
								'Resources' => false
							),
				'theme'=> array(
					'name'=>'default'
					),
				'time'=>array(
						'defaultTimeZone'=>'Australia/Melbourne'
					)
			);

?>