<?php
/*
Plugin Name: ВП Типограф Лайт
Version: 2.3.5
Plugin URI: http://iskariot.ru/wordpress/typo/#typo-light
Description: Легкая версия типографа только с основной функциональностью (рекомендуется для большинства блогов) - обработка кавычек, тире, спецсимволов вне безопасных блоков (pre, code, samp, textarea, script), правка кавычек внутри code, кликабельные ссылки в комментариях. Также правится неправильное форматирование TinyMCE.
Author: Сергей М.
Author URI: http://iskariot.ru/
*/
/*
 * Положить в /wp-content/plugins/
 * Зайти в систему администрирования WordPress и на вкладке "Плагины" активировать плагин
 * Совместимо со всеми версиями Wordpress
*/
/*  Основан на идее "Типографа" от Оранского Максима и Макарова Александра
 * http://rmcreative.ru/article/programming/typograph/  
  * ------------------------------------------------------------
 * использует скрипт Кавычкер Дмитрия Смирнова
 * http://spectator.ru/download
 * ------------------------------------------------------------
 * а также Format Control  от Владимира Колесникова
 * http://blog.sjinks.org.ua/wordpress/patches/224-formatcontrol-plugin-to-solve-formatting-bugs-in-wordpress/ 
 */
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIEIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

//флаг, установите в false, если не нужен неразрывный пробел &nbsp;
//(надо, если используете выключку по ширине justify в оформлении текстовых блоков)
//хотя, конечно, при этом тире может переноситься на следующую строку
global $typoIsNBSP;
$typoIsNBSP=true;
 
/**
* ИНТЕРФЕЙС ПЛАГИНА
* 
*
*/
//удаляем фильтры
	remove_filter('category_description', 'wptexturize');
	remove_filter('single_post_title', 'wptexturize');
	remove_filter('the_title', 'wptexturize');
	remove_filter('the_content', 'wptexturize');
	remove_filter('the_excerpt', 'wptexturize');
	remove_filter('link_title', 'wptexturize');
	remove_filter('single_cat_title', 'wptexturize');
	remove_filter('single_tag_title', 'wptexturize');
	remove_filter('single_post_title', 'wptexturize');
	remove_filter('comment_text', 'wptexturize');
	remove_filter('comment_text', 'convert_chars'); 
	remove_filter('comment_text', 'make_clickable',9);
	remove_filter('comment_text', 'wpautop', 30);
//добавляем свои
	add_filter('single_post_title', 'typoFilterHeader', 9);
	add_filter('the_title', 'typoFilterHeader', 9);
	add_filter('link_title', 'typoFilterHeader', 9);
	add_filter('list_cats', 'typoFilterHeader', 9);
	add_filter('single_cat_title', 'typoFilterHeader', 9);
	add_filter('single_tag_title', 'typoFilterHeader', 9);
	add_filter('single_post_title', 'typoFilterHeader', 9);
	add_filter('the_content', 'typoFilterText', 9);
	add_filter('the_excerpt', 'typoFilterText', 9);
	add_filter('category_description', 'typoFilterText', 9);
	add_filter('comment_text', 'typoFilterComment', 9);


/**
* ФОРМАТИРУЕМ ЗАГОЛОВКИ
* 
*
*/
	function typoFilterHeader($text, $flag=false){
		
		if(!$flag) {
			$text=preg_replace('~(\s*)\.$~','', $text);
			}

		//апостроф апострофом
		$text = preg_replace("('|&#146;)", "&#39;", $text);
		//отключено, потому что надо давать возможность вставлять самому!
		//правим неправильные кавычки
		//$text = preg_replace("(«|»|„|“|”|&quot;|&ldquo;|&rdquo;)", "\"", $text);
		//правим неправильные тире
		//$text = preg_replace("(&ndash;|&minus;|–|−|—|—|—)", "-", $text);
		
		$replace=array(
            // Знаки (c), (r), (tm)
			'~\((c|C|с|С)\)~i' 	=> '&copy;',
			'~\((r|R)\)~i' 	=>	'<sup><small>&reg;</small></sup>',
			'~\((tm|TM|тм|ТМ)\)~i'	=>	'<sup>&trade;</sup>',
			//знак умножения
			'~\b(\d+)(х|x)(\d+)\b~' => '$1&times;$3',
			
			//Многоточие
			//Больше 5 - авторский знак
			"~(?<!\.)\.{2,5}(?!\.)~" => "...",
			//правим заодно правильную пунткуацию у восклицательного и вопросительного знаков
			"~([\?!])\.{2,5}(?!\.)~" => "$1..",
			//закомментируйте предыдущее и раскомментируйте следующую строку,
			//если нужен знак многоточия
			//"~(?<!\.)\.{2,5}(?!\.)~" => "&hellip;",
			
			//оторвать тире от знаков препинания
			//какбе учитываем смайлики
			'~-([\(\.,])~' => '- $1',
			'~([\)\.,])-~' => '$1 -',
			 
			// Разносим неправильные кавычки
			'~([^"]\w+)"(\w+)"~' => '$1 "$2"',
			'~"(\w+)"(\w+)~' => '"$1" $2',
			
			// Оторвать скобку от слова
			'~(\w)\(~' => '$1 (',
			'~\)(\w)~' => ') $1',
			
			//неразрывный до кавычки
			'~&nbsp;"~' => ' "',
			//забытый пробел после тире в начале строки
			"~(^|\n)(--?)(?!\s|-)~" => "$1- ",

			//дефисы перед закрывающими тегами
			//ну вообще, это, наверное, зря
			"~-</~" => "- </",
			
			 // Знак дефиса или два-три знака дефиса подряд — на знак длинного тире.
			// + Нельзя разрывать строку перед тире
			// Добавлена обработка диалогов
			"/( |&nbsp;|(?:(?U)<.*>))(--?-?)(?=\s)/" => '$1&mdash;',
			"~(\n|^)(--?)(?=\s)~" => "$1&mdash;",
			);
			$text=preg_replace(array_keys($replace), array_values($replace), $text);/**/
			
			//правим неразрывный до тире
		global $typoIsNBSP;
		if($typoIsNBSP) {
			$replace=array("(( |\t|&nbsp;)+&mdash;)" => '&nbsp;&mdash;',);
			$text=preg_replace(array_keys($replace), array_values($replace), $text);/**/
			}
			
		//ИНТЕРВАЛЬНЫЕ ТИРЕ
		$pre_days='(понедельник|вторник|среда|четверг|пятница|суббота|воскресенье)';
		$pre_month='((январ|феврал|апрел|июн|июл|сентябр|октябр|ноябр|декабр)(ь|я)|(март|август)(а)?)';
		
		$replace=array(
			// Знак дефиса, ограниченный с обоих сторон цифрами — на знак минуса
			//убрано, потому что так часто публикуются счета, номера телефонов и прочее.
			//'/(?<=\d)-(?=\d)/' => '&minus;',
			//'/(?<=\s)-(?=\d)/' => '&minus;',
			//'/(?<=\d)-(?=\s)/' => '&minus;',
			
			//диапазоны дат, периодов?
			//тоже всегда можно закомментировать ^_^
			'/([^\d]\d{4})(&minus;|-)(\d{4}[^\d])/' => '\\1&mdash;\\3',
			'/'.$pre_days.'( |&nbsp;)?(-|&minus;|&mdash;)( |&nbsp;)?'.$pre_days.'/i' => '$1&mdash;$5',
			'/('.$pre_month.')( |&nbsp;)?(-|&minus;|&mdash;)( |&nbsp;)?(\d|'.$pre_month.')/i' => '$1&mdash;$10',
			'/('.$pre_month.'|\d)( |&nbsp;)?(-|&minus;|&mdash;)( |&nbsp;)?('.$pre_month.')/i' => '$1&mdash;$10',
			/**/
			
		// Спецсимволы для 1/2 1/4 3/4
			'~\b1/2\b~'	=> '&frac12;',
			'~\b1/4\b~' => '&frac14;',
			'~\b3/4\b~' => '&frac34;',
	
			//Плюс-минус
			'~([^\+]|^)\+-~' => '$1&plusmn;',
			
			//отбиваем кавычки от пунктуации
			'~(^|\s)([,\.!?:-;^%])(")~' => '$1$2 $3',
			
			);
		$text=preg_replace(array_keys($replace), array_values($replace), $text);/**/

		//вносим кавычки под тег, если они стоят вне (позже вернем)
		$text = preg_replace('~"(<[^\/][^>]*>)~','$1"',$text);
		$text = preg_replace('~(<\/[^>]*>)"~','"$1',$text);
		
		//КАВЫЧКИ
		// Использован "кавычкер" Version 3.0
        // Copyright (c) Dmitry Smirnov (Nudnik.ru)
		  $text = preg_replace( "/\"\"/i", "\"\"", $text );
	      //$text = preg_replace( "/\"\.\"/i", "&quot;.&quot;", $text );
	      $_text = "\"\"";
		  
      /*while ($_text != $text)
	      { 
		        $_text = $text;
		        $text = preg_replace( "/(^|\s|\201|\200|>)\"([0-9A-Za-z\'\!\s\.\?\,\-\&\;\:\_\200\201]+(\"|&#148;))/i", "\\1&ldquo;\\2", $text );
		       $text = preg_replace( "/((&ldquo;)([A-Za-z0-9\'\!\s\.\?\,\-\&\;\:\200\201\_]*)[A-Za-z0-9][\200\201\?\.\!\,]*)\"/i", "\\1&rdquo;", $text );
		      }//while/**/
			$text = preg_replace("#(\>|^)#", "$1 ", $text);
			
	        $text = preg_replace ("/([¬(\s\"])(\")([^\"]*)([^\s\"(])(\")/", "\\1`laquo;\\3\\4`raquo;", $text); 
	        // что, остались в тексте нераставленные кавычки? значит есть вложенные!
	        if (strrpos ($text, '"'))
	        { 
	            // расставляем оставшиеся кавычки (еще раз).
	            $text = preg_replace ("/([¬(\s\"])(\")([^\"]*)([^\s\"(])(\")/", "\\1`laquo;\\3\\4`raquo;", $text); 
	            // расставляем вложенные кавычки
	            // видим: комбинация из идущих двух подряд открывающихся кавычек без закрывающей
	            // значит, вторая кавычка - вложенная. меняем ее и идущую  после нее, на вложенную
//	        while (preg_match ("/(&laquo;)([^»]*)(&laquo;)([^»]*)(&raquo;)/", $text, $regs))
	        while (preg_match ("/(`laquo;)([^`]*)(`laquo;)([^`]*)(`raquo;)/", $text, $regs)) {
				$text = str_replace ($regs[0], "{$regs[1]}{$regs[2]}&bdquo;{$regs[4]}&ldquo;", $text);
				}
	        } ; 
			
			$text = preg_replace("#\> #", ">", $text);
			
			//заменяем елочки обратно
			$text = preg_replace("#`(l|r)aquo;#", "&$1aquo;", $text);
		
		//если запрещены неразрывные, убираем их
		if(!$typoIsNBSP){
			$text=preg_replace("~&nbsp;~", " ", $text);/**/
		}
		
		$text=preg_replace("~^&nbsp;~", "", $text);/**/
		
		return trim($text);
	}

/**
* Коллбэк для безопасных блоков
* 
*
*/
	function _stack($matches = false){
		static $safe_blocks = array();
		if ($matches !== false){
			$key = '<'.count($safe_blocks).'>';
			$safe_blocks[$key] = $matches[0];
			return $key;
		}
		else{
			$tmp = $safe_blocks;
			unset($safe_blocks);
			return $tmp;
		}
	}

/**
* ФОРМАТИРУЕМ ТЕКСТ
* 
*
*/
	function typoFilterText($text){
		
		//если в code есть переносы автоматически оборачиваем в pre
		//$text=preg_replace("~(?!<pre[^>]*>)\s*(<code[^>]*>[^>]*\n[^>]*<\/code>)\s*(?!<\/pre>)~","\n<pre>$1</pre>\n",$text);
		//в пре форматим внутренние code 
		preg_match_all( "#<pre([^>]*)>\s*<code([^>]*)>(.*)<\/code>\s*<\/pre>#isU", $text, $matches ); 
		$n = count( $matches[1] );
		for($i=0;$i<$n;$i++){
			$result = preg_replace("~<(\/?)code([^>]*)>~","<$1~~code$2>", $matches[3][$i]);
			$text = str_replace( $matches[0][$i], "<pre".$matches[1][$i]."><code".$matches[2][$i].">".$result."</code></pre>", $text );
		}
		
		$_safeBlocks = array(
			'<code[^>]*>' => '<\/code>',
			'<pre[^>]*>' => '<\/pre>',
			'<script[^>]*>' => '<\/script>',
			'<style[^>]*>' => '<\/style>',
			'<textarea[^>]*>' => '<\/textarea>',
			'<form[^>]*>' => '<\/form>',
			'<samp>' => '<\/samp>',
			'<kbd>' => '<\/kbd>',
			'{\[' => '\]}',
			'<!--' => '-->',

		);

		//ВНЕ БЕЗОПАСНЫХ БЛОКОВ
		$pattern = '(';
		foreach ($_safeBlocks as $start => $end){
			$pattern .= "$start.*$end|";
		}
		$pattern .= '<[^>]*[\s][^>]*>|\[[^\]]*\])';
		//$pattern .= '<[^>]*[\s][^>]*>)';
		$text = preg_replace_callback("~".$pattern."~isU", '_stack', $text);
			//тире кавычки и пр. из предыдущей функции
			$text = typoFilterHeader($text, true);
		$text = strtr($text, _stack());
		
		//ВНЕ КОДОВ, ПРЕ, ПРИМЕРОВ делаем пост-обработку
		$pattern = '(';
		foreach ($_safeBlocks as $start => $end){
			$pattern .= "$start.*$end|";
		}
		$pattern .= '\[[^\]]*\])';
		$text = preg_replace_callback("~".$pattern."~isU", '_stack', $text);
			//выносим пробел за тег (нет, потому что он может быть в коде)
			//$text = preg_replace("~(<[^\/][^>]+>) +~",' $1',$text);
			//$text = preg_replace("~ +(<\/[^>]+>)~",'$1 ',$text);
			//ну и за ссылку
			//$text = preg_replace("~(?!\s)(<a[^>]*>)\s?~",' $1',$text);
			//выносим кавычки за ссылку
			$text=preg_replace('/<a([^>]+)>&laquo;([^<]+)&raquo;<\/a>/usi', '&laquo;<a\1>\2</a>&raquo;', $text);
			$text=preg_replace('/<a([^>]+)>&bdquo;([^<]+)&ldquo;<\/a>/usi', '&bdquo;<a\1>\2</a>&ldquo;', $text);
		$text = strtr($text, _stack());
		
		//В ЦИТАТАХ МЕНЯЕМ МЕСТАМИ КАВЫЧКИ
		/*preg_match_all( "#<blockquote([^>]*)>(.+)<\/blockquote>#isU", $text, $matches ); 
		$n = count( $matches[0] );
		for($i=0;$i<$n;$i++){
		$replace=array(
			//вот эти кавычки, Дэвид Блейн!
			"~&laquo;~" => "`bdquo;",
			"~&raquo;~" => "`ldquo;",
			"~&bdquo;~" => "&laquo;",
			"~&ldquo;~" => "&raquo;",
			"~`(b|l)~" => "&$1",
		);
		$result = trim(preg_replace(array_keys($replace), array_values($replace), $matches[2][$i]));
		$text = str_replace( $matches[0][$i], "<blockquote".$matches[1][$i].">".$result."</blockquote>", $text );
		}/**/
		
		//В БЕЗОПАСНЫХ БЛОКАХ
		preg_match_all( "#<code([^>]*)>((.|\s)+)<\/code>#isU", $text, $matches ); 
		$n = count( $matches[0] );
		for($i=0;$i<$n;$i++){
		$replace=array(
			//делаем машинописные кавычки
			"~(\"|“|”|„)~" => '"',
			//и правильный для кода дефис
			"~–|−~" => "-",
			//спецсимволы - 
			//"#&(?!amp;)#" => "&amp;",
			//делаем теги в code текстом
			"~<~" => "&lt;",
			"~>~" => "&gt;",
			//апостроф - апострофом
			"~(&#39;|&#146;)~" => "'",
			//возвращаем
			"/~~code/" => "code",
		);
		$result = trim(preg_replace(array_keys($replace), array_values($replace), $matches[2][$i]));
		$text = str_replace( $matches[0][$i], "<code".$matches[1][$i].">".$result."</code>", $text );
		}/**/
		
		
		
		//на всякий случай - против косячного плагина
		$text = preg_replace('~(<p>(\s|\n)*<script)~', "<script", $text);
		$text = preg_replace('~(<\/script>(\s|\n)*</p>)~', "</script>", $text);
		$text=preg_replace("~\[CDATA\[(\n\n|</p>\n<p>)~","[CDATA[\n",$text);
		
		return $text;
	}


/**
* ФОРМАТИРУЕМ КОММЕНТАРИИ
* 
*
*/
	function typoFilterComment($text){
	
		/**/
		//Типа "Markdown"
/*			$replace=array(
				"#\*{2,}([^\*]+)\*+#" => "<strong>\\1</strong>",
				"#_{2,}([^_]+)_+#" => "<em>\\1</em>",
				"#(^|\n)\s*(>|&gt;)\s*([^\n]+)#" => "<blockquote>&gt; \\3<br /></blockquote>",
				"#(</blockquote>)(\s*)(<blockquote>)#" => "",
				"#(<br /></blockquote>)#" => "</blockquote>",
				"#({\[|\[{)([^\n]*?)(\]}|}\])#" => "<code>\\2</code>",
				"#({\[|\[{)([^`]*?)(\]}|}\])#" => "<pre><code>\\2</code></pre>",
			);
			$text = preg_replace(array_keys($replace), array_values($replace), $text);
		/**/
			
		//бывают косяки от сторонних плагов, когда </p> идет внутри </a> - а поставим мы пробел!
		$text = preg_replace( "~<\/p>~", " </p>", $text );
			
		//кликабельные ссылки
		$text=preg_replace("~(^|\s|-|:| |\()(http(s?)://|(www\.))((\S{25})(\S{5,})(\S{15})([^\<\s.,>)\];'\"!?]))~i", "\\1<a href=\"http\\3://\\4\\5\">\\4\\6...\\8\\9</a>", $text);
		$text=preg_replace("~(^|\s|-|:|\(| |\xAB)(http(s?)://|(www\.))((\S+)([^\<\s.,>)\];'\"!?]))~i", "\\1<a href=\"http\\3://\\4\\5\">\\4\\5</a>", $text);
		
		//убираем / в конце ссылок без вложенности
		$text = preg_replace( "~(<a[^>]*>[^\/]+)\/<\/a>~", "$1</a>", $text );
		
		//Пустые строки - в топку
		//$text = preg_replace( "~<p>(\n|\s)*</p>~", "", $text );
		
		//Каждый абзац в комментариях - параграфом (вне pre)
		$pattern = '(<pre[^>]*>.*<\/pre>|<code[^>]*>.*<\/code>)';
		$text = preg_replace_callback("~".$pattern."~isU", '_stack', $text);
			$text = preg_replace("/(\n)?(.+?)(?:\n\s*\n*|\z)/s","<p>$2</p>",$text);
		$text = strtr($text, _stack());
						
		//правим цитаты
		$text = preg_replace('~<p><blockquote([^>]*)>~i', "<blockquote$1><p>", $text);
        $text = str_replace('</blockquote></p>', '</p></blockquote>', $text);
		
		//и еще косяки от сторонних плагинов - пустые абзацы
		$text = preg_replace("~<p>(\s)*<\/p>~", "", $text);
		$text = preg_replace("~<p>\s*<p>~", "<p>", $text);
		$text = preg_replace("~<\/p>\s*<\/p>~", "</p>", $text);
		
		//правильная обработка из прошлой функции
		$text = typoFilterText($text);
		
		return $text;
	}


/**
* ИСПРАВЛЕНИЕ ОШИБОК ВЕРСТКИ
* 
*
*/
	function typoFilterWPautop($pee, $br = 1)
        {
			$pee=preg_replace("~\[CDATA\[(\n\n|</p>\n<p>)~","[CDATA[\n",$pee);
			$pee = preg_replace('~(<p>(\s|\n)*<script)~', "<script", $pee);
			$pee = preg_replace('~(<\/script>(\s|\n)*</p>)~', "</script>", $pee);
	
			
			$pee = preg_replace('~(<hr[^>]*>)~', "$1<p>", $pee);/**/
	
            $pee = $pee . "\n"; // just to make things a little easier, pad the end
            $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
            // Space things out a little
            $allblocks = '(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr)';
            $pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
            $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
            $pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
            if ( strpos($pee, '<object') !== false ) {
                $pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee); // no pee inside object/embed
                $pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
            }
            $pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
            $pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $pee); // make paragraphs, including one at the end
            $pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
            $pee = preg_replace('!<p>([^<]+)\s*?(</(?:div|address|form)[^>]*>)!', "<p>$1</p>$2", $pee);
            $pee = preg_replace("/<p>\\s*(<{$allblocks}.*?)<\\/p>/ism", '$1', $pee);
            $pee = preg_replace( '|<p>|', "$1<p>", $pee );
            $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
            $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
            $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
            $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
			$pee = preg_replace_callback("/<p>(.*?)(<\\/{$allblocks}>)/s", 'replace_callback', $pee);
            $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
            $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
            if ($br) {
                $pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', create_function('$matches', 'return str_replace("\n", "<WPPreserveNewline />", $matches[0]);'), $pee);
                $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
                $pee = str_replace('<WPPreserveNewline />', "\n", $pee);
            }
            $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
            $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
            if (strpos($pee, '<pre') !== false) {
                $pee = preg_replace_callback('!(<pre.*?>)(.*?)</pre>!is', 'clean_pre2', $pee);
            }
            $pee = preg_replace( "|\n</p>$|", '</p>', $pee );
            $pee = preg_replace('/<p>\s*?(' . get_shortcode_regex() . ')\s*<\/p>/s', '$1', $pee); // don't auto-p wrap shortcodes that stand alone
		    return $pee;
        }

	function clean_pre2($matches) {
	if ( is_array($matches) )
		$text = $matches[1] . $matches[2] . "</pre>";
	else
		$text = $matches;

	$text = str_replace('<br />', '', $text);
	$text = str_replace('<p>', "\n", $text);
	$text = str_replace('</p>', '', $text);

	return $text;
	} 	
	
	function replace_callback($matches)
		{
			if ('</p>' == $matches[2]) {
				return '<p>' . $matches[1] . $matches[2];
			}

			return $matches[1] . $matches[2];
		} 

remove_filter('the_content', 'wpautop', 30);
remove_filter('the_excerpt', 'wpautop', 30);
//уже убраны
//remove_filter('comment_text', 'wpautop', 30);

add_filter('the_content', 'typoFilterWPautop', 30);
add_filter('the_excerpt', 'typoFilterWPautop', 30);
//уже используется другая обработка
//add_filter('comment_text', 'typoFilterWPautop', 30);/**/

//ШОРТКОДЫ ДОЛЖНЫ ИДТИ ПОСЛЕ wpautop
remove_filter('the_content', 'do_shortcode', 11);
add_filter('the_content', 'do_shortcode', 43);

?>