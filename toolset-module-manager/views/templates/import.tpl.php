<?php
// deny access
if( !defined('ABSPATH') ) die('Security check');
if(!current_user_can(MODMAN_CAPABILITY)) die('Access Denied');
?>

<div class="import-module">

	<div class="import-module-header">
		<p>
			<?php _e('This screen is used to import modules.
			You can see the module details like Name, Description and detailed contents.
			Also you can choose what to import and what not, depending on what exists already on your Wordpress Installation.','module-manager'); ?>
		</p>

	<?php
	$step=0;
	if (isset($_GET['step'])) $step=intval($_GET['step']);

	// which step of the process
	switch ($step)
	{
		case 1:
			?>
				<p class="import-module-steps"><?php printf(__('Import: Step <strong>%d</strong> of %d','module-manager'),1,2); ?></p>
	</div> <!-- .import-module-header -->
			<?php
			if (
				isset($_POST['modman-import-field']) &&
				wp_verify_nonce( $_POST['modman-import-field'], 'modman-import-action' ) &&
				isset($_FILES['import-file'])
			)
			{
				if (isset($_FILES['import-file']['error']) && $_FILES['import-file']['error']>0)
				{
					?>
						<div class='error'><p><?php _e('Upload Error','module-manager'); ?></p></div>
					<?php
				}
				else
				{
					$info=ModuleManager::importModuleStepByStep(1, array(
										'file'=>$_FILES['import-file']
										));
					if (is_wp_error($info))
					{
						?>
							<div class='error'><p><?php echo $info->get_error_message($info->get_error_code()); ?></p></div>
						<?php
					}
					else
					{
						$step=2;
						$url.='&step='.$step;
						?>
							<div class="import-module-group import-module-info">
								<h3><?php _e('Module Information','module-manager'); ?></h3>
								<div>
									<h4>
										<?php _e('Module Name:','module-manager'); ?>
									</h4>
									<p>
										<?php
										if ( isset( $info[ MODMAN_MODULE_INFO ][ 'name' ] ) )
											echo $info[ MODMAN_MODULE_INFO ][ 'name' ];
										?>
									</p>
								</div>
								<?php if (isset($info[MODMAN_MODULE_INFO]['description'])): ?>
									<div>
										<h4><?php _e('Module Description:','module-manager'); ?></h4>
										<p class='import-module-description'>
											<?php echo ModuleManager::sanitizeTags(stripslashes($info[MODMAN_MODULE_INFO]['description'])); ?>
										</p>
									</div>
								<?php endif; ?>
							</div>


							<form name="modman-import-form" action="<?php echo $url; ?>" method="post">
								<div class="import-module-group">
									<input type="hidden" name="info" value="<?php echo $info[MODMAN_MODULE_TMP_FILE]; ?>" />
									<?php wp_nonce_field('modman-import-action-2','modman-import-field-2'); ?>
									<div class='import-module-contents'>
										<h3><?php _e('Module Contents:','module-manager'); ?></h3>
										<?php
											foreach ($info as $section_id=>$items)
											{
												if (!in_array($section_id, array(MODMAN_MODULE_INFO,MODMAN_MODULE_TMP_FILE)))
												{
													?>
													<div class="import-module-contents-item">
														<h4>
															<?php
																if (isset($sections[$section_id]))
																	echo $sections[$section_id]['title'];
																else
																	echo $section_id;
															?>
														</h4>
														<?php
															foreach ($items as $item)
															{
                                                                $checked='';
                                                                if (!isset($item['exists']) || !$item['exists'] || (isset($item['is_different'])&&$item['is_different']))
                                                                {
                                                                    $checked='checked="checked"';
                                                                }
														?>
															<p>
																<input type="checkbox" name="items[<?php echo $section_id; ?>][<?php echo $item['id']; ?>]" value="1" <?php echo $checked; ?> />
																<span class="import-module-info">
																	<?php
																		if (isset($item['exists'])&&$item['exists'])
																		{
																			if (isset($item['is_different'])&&$item['is_different'])
																			{

																				?>
																					<i class="icon-refresh"></i>
																					<span class="import-module-name"><?php echo $item['title']; ?></span>
																				<?php
																					_e('&ndash; A different version is installed, it will be overwritten','module-manager');
																			}
																			else
																			{
																				?>
																					<i class="icon-check"></i>
																					<span class="import-module-name"><?php echo $item['title']; ?></span>
																				<?php
																					_e('&ndash; Same version is installed','module-manager');
																			}
																		}
																		else
																		{
																			?>
																				<i class="icon-plus"></i>
																				<span class="import-module-name"><?php echo $item['title']; ?></span>
																			<?php
																				_e('&ndash; A new item will be added','module-manager');
																		}
																	?>
																</span>
															</p>
															<!--<pre>
															<?php //print_r($item); ?>
															</pre>-->
														<?php
															}
														?>
														</div> <!-- import-module-contents-item -->
												<?php }
											} ?>
								</div>
							</div> <!-- import-module-group -->
							<input type="submit" class="button button-primary button-large" value="<?php echo esc_attr(__('Import selected items','module-manager')); ?>" />
						</form>

						<?php
					}
				}
			}
			else
			{
				?>
					<div class='error'><p><?php _e('No File was uploaded','module-manager'); ?></p></div>
				<?php
			}
			break;

		case 2:
			?>
				<p class="import-module-steps"><?php printf(__('Import: Step %d of <strong>%d</strong>','module-manager'),2,2); ?></p>
				</div> <!-- .import-module-header -->
			<?php
			if (
				isset($_POST['modman-import-field-2']) &&
				wp_verify_nonce( $_POST['modman-import-field-2'], 'modman-import-action-2' ) &&
				isset($_POST['items']) && isset($_POST['info'])
			)
			{
				// get import results
				$results=ModuleManager::importModuleStepByStep(2, array(
									'info'=>$_POST['info'],
									'items'=>$_POST['items']
									));
				if (!is_wp_error($results))
				{
					$hasError=false;
					$import_output='';
					// print info AFTER the module manager message
					ob_start();
					if (isset($results[MODMAN_MODULE_INFO]))
					{
						?>
							<div class="import-module-group import-module-info">
								<h3><?php _e('Module Information','module-manager'); ?></h3>
								<div>
									<h4>
										<?php _e('Module Name:','module-manager'); ?>
									</h4>
									<p>
										<?php
										if ( isset( $results[ MODMAN_MODULE_INFO ][ 'name' ] ) )
											echo $results[ MODMAN_MODULE_INFO ][ 'name' ];
										?>
									</p>
								</div>
								<?php if (isset($results[MODMAN_MODULE_INFO]['description'])): ?>
									<div>
										<h4><?php _e('Module Description:','module-manager'); ?></h4>
										<p class='import-module-description'>
											<?php echo ModuleManager::sanitizeTags(stripslashes($results[MODMAN_MODULE_INFO]['description'])); ?>
										</p>
									</div>
								<?php endif; ?>
							</div>
						<?php
						unset($results[MODMAN_MODULE_INFO]);
					}
					?>

					<div class="import-module-group">
						<h3><?php _e('Module import details:','module-manager'); ?></h3>
						<ul>
							<?php
								foreach ($results as $section_id=>$data)
								{

									if (isset($data['updated']) && $data['updated']>0)
									{
										?>
										   <li><?php printf(__('(%d) %s were <strong>overwritten</strong>', 'module-manager'), $data['updated'], $sections[$section_id]['title']); ?></li>
										<?php
									}
									if (isset($data['new']) && $data['new']>0)
									{
										?>
										   <li><?php printf(__('(%d) %s were <strong>created</strong>', 'module-manager'), $data['new'], $sections[$section_id]['title']); ?></li>
										<?php
									}
									if (isset($data['failed']) && $data['failed']>0)
									{
										?>
										   <li><?php printf(__('(%d) %s <strong>failed</strong> to import', 'module-manager'), $data['failed'], $sections[$section_id]['title']); ?></li>
										<?php
									}
									if (isset($data['errors']) && !empty($data['errors']))
									{
										$hasErrors=true;
										foreach ($data['errors'] as $err)
										{
										?>
										   <div class='error'><p><?php echo $err; ?></p></div>
										<?php
										}
									}
								}
							?>
						</ul>
					</div>
					<p>
						<a href="<?php echo $mm_url; ?>" class="button button-primary button-large"><?php _e('Go to modules','module-manager') ?></a>
					</p>
					<?php
					$import_output=ob_get_clean();
					if ($hasError)
					{
						?>
							<div class='error'><p><?php _e('Import failed','module-manager'); ?></p></div>
						<?php
					}
					else
					{
						?>
							<div class='updated'><p><?php _e('Import succesful','module-manager'); ?></p></div>
						<?php
					}
					echo $import_output;
				}
				else
				{
					?>
						<div class='error'><p><?php $results->get_error_message($results->get_error_code()); ?></p></div>
					<?php
				}
			}
			elseif (!isset($_POST['items']))
			{
				?>
					<div class='error'><p><?php _e('No items were selected','module-manager'); ?></p></div>
				<?php
			}
			else
			{
				?>
					<div class='error'><p><?php _e('No Module information was given','module-manager'); ?></p></div>
				<?php
			}
			break;

		default:
			$step=1;
			$url.='&step='.$step;
			?>

				<form name="modman-import-form" enctype="multipart/form-data" action="<?php echo $url; ?>" method="post">
				<?php wp_nonce_field('modman-import-action','modman-import-field'); ?>
				<table class="widefat" id="modman_import_table">
				<thead>
					<tr>
						<th><?php _e('Import Module','module-manager'); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<label for="upload-modules-file"><?php _e('Select the module .zip file to upload:&nbsp;','module-manager'); ?></label>

							<input type="file" id="upload-modules-file" name="import-file" />

							<input id="modman-import" class="button-primary" type="submit" value="<?php echo esc_attr(__('Import','module-manager')); ?>" name="import" />
							</td>
						</tr>
					</tbody>
				</table>
				</form>
			<?php
			break;
	} ?>
