<?php
/**
 * Seo Package
 *
 * @package    Seo
 * @version    0.1
 * @author     Hinashiki
 * @license    MIT License
 * @copyright  2015 - Hinashiki
 * @link       https://github.com/hinashiki/fuelphp-seo
 */
?>
<meta name="google" content="nositelinkssearchbox">
<meta http-equiv="X-UA-compatible" content="IE=edge">
<?= Seo_Html::nocache() ?>
<?= Seo::instance()->noindex() ?>
<?= Seo::instance()->canonical() ?>
<link rel="start" href="<?= \Config::get('base_url') ?>" />
<?php if(isset($prev_next)): ?>
	<?= Seo::instance()->prev_next($prev_next) ?>
<?php else: ?>
	<?= Seo::instance()->prev_next() ?>
<?php endif; ?>
