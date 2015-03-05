<?php

namespace Qlake\Html;

class HtmlBuilder
{

	protected $charset = 'UTF-8';


	public function __construct()
	{
	}



	public function getCharSet()
	{
		return $this->charset;
	}



	public function createElement($tag, array $attributs = [], $content = null, $closeTag = false)
	{
		$html = '<' . $tag . (empty($attributs) ? '' : ' ' . $this->createAttributeStrings($attributs));

		if($content === null)
		{
			return (!$closeTag ? $html . ' />' : $html . '>' . '</' . $tag . '>') . PHP_EOL;
		}
		else
		{
			return ($html . '>' . $content . '</' . $tag . '>') . PHP_EOL;
		}
	}



	public function createAttributeStrings(array $attributs)
	{
		$html = [];

		foreach ($attributs as $key => $value)
		{
			$html[] = $key . '=' . '"' . htmlentities($value, ENT_QUOTES, $this->charset, false) . '"';
		}

		return implode($html, ' ');
	}



	public function decode($text)
	{
		return htmlspecialchars_decode($text, ENT_QUOTES);
	}



	public function encode($text)
	{
		return htmlspecialchars($text, ENT_QUOTES, $this->getCharSet());
	}



	public function encodeArray(array $array)
	{
		$encodedArray = [];

		foreach($array as $key => $value)
		{
			if(is_string($key))
			{
				$key = htmlspecialchars($key, ENT_QUOTES, $this->getCharSet());
			}

			if(is_string($value))
			{
				$value = htmlspecialchars($value, ENT_QUOTES, $this->getCharSet());
			}
			elseif(is_array($value))
			{
				$value = self::encodeArray($value);
			}

			$encodedArray[$key] = $value;
		}

		return $encodedArray;
	}



	public function style($url, array $attributes = [])
	{
		$defaults = ['href' => $url, 'rel' => 'stylesheet', 'type' => 'text/css', 'media' => 'all'];

		$attrs = array_merge($defaults, $attributes);

		return $this->createElement('link', $attrs);
	}



	public function script($url, array $attributes = [])
	{
		$defaults = ['src' => $url, 'type' => 'text/javascript'];

		$attrs = array_merge($defaults, $attributes);

		return $this->createElement('script', $attrs, null, true);
	}



	public function favicon($url)
	{
 		return $this->createElement('link', ['href' => $url, 'rel' => 'shortcut icon']);
	}



	public function meta(array $attributs)
	{
		return $this->createElement('meta', $attributs);
	}



	public function label($label, array $attributs = [])
	{
		return $this->createElement('label', $attributs, $label);
	}



	public function text($type, $name = '', $value = '', array $attributs = [])
	{
		$attributs['type'] = 'text';

		$attributs['name'] = $name;

		$attributs['value'] = $value;

		return $this->createElement('input', $attributs);
	}



	public function password($name = '', $label = '', array $attributs = [])
	{
		$attributs['type'] = 'password';

		$attributs['name'] = $name;

		return $this->createElement('input', $attributs);
	}



	public function hidden($name = '', $value = '', array $attributs = [])
	{
		$attributs['type'] = 'hidden';

		$attributs['value'] = $value;

		return $this->createElement('input', $attributs);
	}



	public function image($src, $alt = '', array $attributs = [])
	{
		$attributs['src'] = $src;

		$attributs['alt'] = $alt;

		return $this->createElement('img', $attributs);
	}



	public function imageLink($url, $src, $alt = '', array $attributs = [])
	{
		$attributs['src'] = $src;

		$attributs['alt'] = $alt;

		$imgTag = $this->createElement('img', $attributs);

		$linkAttributs['href'] = $url; 

		return $this->createElement('a', $linkAttributs, $imgTag);
	}



	public function link($url, $label, $target = null, array $attributs = [])
	{
		$attributs['href'] = $url;

		$attributs['target'] = $target ?: '_self';

		return $this->createElement('a', $attributs, $label);
	}



	public function ul(array $values, array $attributs = [])
	{
		$html = [];

		foreach ($values as $key => $value)
		{
			$html[] = '<li>' . $value . '</li>' . PHP_EOL;
		}

		return $this->createElement('ul', $attributs, implode($html, ''));
	}



	public function ol(array $values, array $attributs = [])
	{
		$html = [];

		foreach ($values as $key => $value) 
		{
			$html[] = '<li>' . $value . '</li>' . PHP_EOL;
		}

		return $this->createElement('ol', $attributs, implode($html, ''));
	}



	public function radio($name, $value, $checked = false, $label = null, array $attributs = [])
	{
		$labelTag = isset($label) ? $this->label($name, $label, ['for', $name]) : '';

		$attributs['type'] = 'radio';

		$attributs['name'] = $name;

		$attributs['value'] = $value;

		if ($checked)
		{
			$attributs['checked'] = 'checked';
		}
		
		return $labelTag . $this->createElement('input', $attributs);
	}



	/*public function radioGroup($name, array $values, $label = null, $attributs = [])
	{
		$labelTag = isset($label) ? $this->label($name, $label, ['for', $name]) : '';

		$html = [];

		$stop = count($value) - 1;

		foreach (range(0, $stop) as $i)
		{
			$attributs['type'] = 'radio';

			$attributs['name'] = $name;

			$attributs['value'] = $value[$i];

			$html[] = $this->createElement('input', $attributs);
		}

		return $label . implode($html, '');
	}*/



	public function checkbox($name, $value, $checked = false, $label = null, array $attributs = [])
	{
		$labelTag = isset($label) ? $this->label($name, $label, ['for', $name]) : '';

		$attributs['type'] = 'checkbox';

		$attributs['name'] = $name;

		$attributs['value'] = $value;

		if ($checked)
		{
			$attributs['checked'] = 'checked';
		}

		return $labelTag . $this->createElement('input', $attributs);
	}



	/*public function checkboxGroup($name, $value, $label = null, $attributs = [])
	{
		if (isset($label))
		{
			$labelAttributs['for'] = $name;

			$label = $this->label($name, $label, $labelAttributs);
		}
		else 
		{
			$label = '';
		}

		if (is_array($value))
		{
			$html = [];

			$stop = count($value) -1 ;

			foreach (range(0, $stop) as $i) 
			{
				$attributs['type'] = 'checkbox';

				$attributs['name'] = $name;

				$attributs['value'] = $value[$i];

				$html[] = $this->createElement('input', $attributs);
			}

			return $label . implode($html, '');
		}
		else
		{
			$attributs['type'] = 'checkbox';

			$attributs['name'] = $name;

			$attributs['value'] = $value;

			return $label . $this->createElement('input', $attributs);
		}
	}*/



	public function select($name, $values, $default = '0', $attributs = [])
	{
		$html = [];

		$attributs['name'] = $name;

		$selected = '';

		if (!$this->is_multi($values))
		{
			foreach ($values as $value => $title)
			{
				if ($value == $default)
				{
					$selected = "selected='selected'";
				}

				$html[] = "\t<option value = '$value' $selected>" . $title . '</option>' . PHP_EOL ;

				$selected = '';
			}
		}
		else
		{
			foreach ($values as $key => $value)
			{
				$html[] = "\t<optgroup label='$key'>" . PHP_EOL;

				foreach ($value as $selectValue => $title)
				{
					if ($selectValue == $default)
					{
						$selected = "selected='selected'";
					}

					$html[] = "\t\t<option value = '$selectValue' $selected>" . $title . '</option>' . PHP_EOL ;

					$selected = '';
				}

				$html[] = "\t</optgroup>" . PHP_EOL;
			}
		}

		$selectTag = '<select ' . $this->createAttributeStrings($attributs) . '>' . PHP_EOL;

		return $this->createElement('select', $attributs, implode($html, ''));
	}



	public function formTable($rows, $singleRow = false, $attributs = [])
	{
		$html = [];

		$attributsTable = null;

		isset($attributs) ? $attributsTable = $this->createAttributeStrings($attributs) : null;

		$html[] = '<table ' . $attributsTable . '>' . PHP_EOL;

		$html[] = '<tbody>' . PHP_EOL;

		foreach ($rows as $row) 
		{
			$singleRow ? $html[] = '<tr>' . PHP_EOL : null;

			foreach ($row as $key => $value) 
			{
				!$singleRow ? $html[] = '<tr>' . PHP_EOL : null;

				if ($key == 'label')
				{
					list($title, $name) = explode('|', $value);

					$html[] = '<td>' . "<label for='$name'> "  . $title . "</label>". '</td>' . PHP_EOL;
				}

				if ($key == 'input')
				{
					$html[] = '<td>' . "<input name='$name' value='$value'" . " />". '</td>'  . PHP_EOL;
				}

				!$singleRow ? $html[] = '</tr>' . PHP_EOL : null;
			}	

			$singleRow ? $html[] = '</tr>' . PHP_EOL : null;
		}

		$html[] = '</tbody>' . PHP_EOL;

		$html[] = '</table>' . PHP_EOL;

		return implode($html, '');
	}



	public function selectRange($name, $range, $values = [], $default = '0', $attributs = [])
	{
		$html = [];

		$attributs['name'] = $name;

		if (strpos($range, '~'))
		{
			list($start, $stop) = explode('~', $range) ;

			if ($start != 0){
				$start--;
				$stop--;
			}
		}
		else
		{
			$start = 0;

			$stop = count($values) -1 ;
		}

		$selected = '';

		$i = 0;

		foreach (range($start, $stop) as $i)
		{
			if ($i == $default)
			{
				$selected = "selected='selected'";
			}

			$html[] = "\t<option value = '$values[$i]' $selected>" . $values[$i][0] . '</option>' . PHP_EOL ;

			$selected = '';
		}

		return $this->createElement('select', $attributs, implode($html, ''));
	}



	public function is_multi($array) 
	{
		$rv = array_filter($array, 'is_array');

		if(count($rv) > 0)
		{
			return true;
		}

		return false;
	}



	public function head($tags = [])
	{
		$tagsArray = [];

		foreach ($tags as $keytag => $valuetag) 
		{
			switch ($keytag) 
			{
				case 'title':

				$this->simpleCloseTag = false;

				$tagsArray[] = '<title>'. $valuetag . '</title>';

				break;

				case  'style':

				$tagsArray[] = $this->addStyle($valuetag) . PHP_EOL;

				break;

				case  'script':

				$tagsArray[] = $this->addScript($valuetag) . PHP_EOL;

				break;

				case 'meta':

				$this->simpleCloseTag = true;

				$tagsArray[] = $this->createElement('meta', $valuetag) . PHP_EOL;

				break;

				case 'twitter':

				$this->simpleCloseTag = true;

				$tagsArray[] = $this->createElement('meta', array('name' => $keytag . ':' . $valuetag['name'], 'content' => $valuetag['content'])) . PHP_EOL;

				break;

				case 'favIcon':

				$tagsArray[] = $this->favIcon($valuetag) . PHP_EOL;

				break;
			}
		}

		return implode($tagsArray, ' ');
	}



}


