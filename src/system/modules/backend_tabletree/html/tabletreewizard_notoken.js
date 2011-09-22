/**
 * Class BackendTabletreeWizard
 *
 * Provide methods to handle back end tasks.
 * @copyright  Thyon Design 2009
 * @author     John Brand <john.brand@thyon.com>
 * @package    BackendTaxonomyWizard
 */
 
var AjaxRequestTabletree =
{

	/**
	 * Toggle the page tree (input field)
	 * @param object
	 * @param string
	 * @param string
	 * @param string
	 * @param integer
	 * @return boolean
	 */
	toggleTabletree: function (el, id, field, name, level)
	{
		el.blur();
		var item = $(id);
		var image = $(el).getFirst();

		if (item)
		{
			if (item.getStyle('display') != 'inline')
			{
				item.setStyle('display', 'inline');
				image.src = image.src.replace('folPlus.gif', 'folMinus.gif');
				new Request().post({'isAjax': 1, 'action':'toggleTabletree', 'id': id, 'state': 1});
			}
			else
			{
				item.setStyle('display', 'none');
				image.src = image.src.replace('folMinus.gif', 'folPlus.gif');
				new Request().post({'isAjax': 1, 'action':'toggleTabletree', 'id': id, 'state': 0});
			}

			return false;
		}

		new Request(
		{
			onRequest: AjaxRequest.displayBox('Loading data ...'),
			onComplete: function(txt, xml)
			{
				var ul = new Element('ul');

				ul.addClass('level_' + level);
				ul.set('html', txt);

				item = new Element('li');

				item.addClass('parent');
				item.setProperty('id', id);
				item.setStyle('display', 'inline');

				ul.injectInside(item);
				item.injectAfter($(el).getParent('li'));

				image.src = image.src.replace('folPlus.gif', 'folMinus.gif');
				AjaxRequest.hideBox();
			}
		}).post({'isAjax': 1, 'action':'loadTabletree', 'id': id, 'level': level, 'field': field, 'name': name, 'state': 1});

		return false;
	}
	
}
