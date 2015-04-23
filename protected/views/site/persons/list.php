<?$this->renderPartial('persons/_small-banner');?>
<?$this->renderPartial('persons/_results', compact('model')); //результаты поиска ?>

<?$this->renderPartial('persons/_list', compact('model'));?>

