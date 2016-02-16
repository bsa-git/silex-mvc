<?php

// app/Services/My/String.php

namespace Services\My;

/**
 * Class - String
 * Running string tasks
 *
 * @category Service
 * @package  app\Services
 * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class String implements ComparableInterface {

    /**
     * @var string primitive string type
     */
    private $str;

    /**
     * Type the default encoding - "UTF-8"
     * 
     * @var string 
     */
    private $encoding = "";

    /**
     * Constructor
     * 
     * @param string $s 
     * @param string $encoding 
     */
    public function __construct($s = "", $encoding = "utf-8") {
        $this->str = $s;
        $this->encoding = $encoding;
    }

    /**
     * Destructor to prevent memory leaks.
     */
    public function __destruct() {
        unset($this);
    }

    /**
     * Returns string representation of the object
     * @return string string representation of the object
     */
    public function __toString() {
        return strval($this->str);
    }

    /**
     * Returns string representation of the object
     * @return string string representation of the object
     */
    public function get() {
        return strval($this->str);
    }

    /**
     * Set string
     * 
     * @param string $s 
     * @param string $encoding 
     * @return Services\StrBox
     */
    public function set($s = "", $encoding = "utf-8") {
        $this->str = $s;
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * Tests if this string begins with the specified suffix.
     * @param String|string $suffix the character sequence to search for
     * @return boolean true if the character sequence represented by the argument is
     * a character
     */
    public function beginsWith($suffix) {
        return (substr($this->str, 0, strlen($suffix)) == $suffix);
    }

    /**
     * Transform encoded string - $encoding
     * 
     * @param string $encoding
     * @return boolean true if the character sequence represented by the argument is
     * a character
     */
    public function toEncoding($encoding) {
        $newStr = iconv($this->encoding, $encoding, $this->str);
        return new self($newStr, $encoding);
    }

    /**
     * Tests if this string begins with the specified suffix (case insensitive).
     * @param String|string $suffix the character sequence to search for
     * @return boolean true if the character sequence represented by the argument is
     * a character
     */
    public function beginsWithIgnoreCase($suffix) {
        $pos = stripos($this->str, strval($suffix));
        if ($pos === false) {
            return false;
        }
        return ($pos === 0);
    }

    /**
     * Returns the character at the specified index
     * @param int $index the index of the char value.
     * @return string the char value at the specified index of this string.
     * The first char value is at index 0.
     * @throws RuntimeException if 0 > $index > strlength
     */
    public function charAt($index) {
        if ($index < 0 || $index > $this->length()) {
            throw new RuntimeException('Index out of bound.');
        }
        return $this->str[$index];
    }

    /**
     * Compares this object with the specified object for order. Returns a negative
     * integer, zero, or a positive integer as this object is less than, equal to,
     * or greater than the specified object
     *
     * @uses Compares this object with the specified string|String for order
     * @param Default_Plugin_Comparable $s - the string to be compared
     * @return int a negative integer, zero, or a positive integer as this object is
     * less than, equal to, or greater than the specified object
     */
    public function compareTo(ComparableInterface $s) {
        return strnatcmp($this->str, $s);
    }

    /**
     * Case Insensitive Comparator
     * @param Comparable $s - the string to be compared
     * @return int a negative integer, zero, or a positive integer as this string is
     * less than, equal to, or greater than the specified string
     */
    public function compareToIgnoreCase(ComparableInterface $s) {
        return strnatcasecmp($this->str, $s);
    }

    /**
     * Concatenates the specified String to the end of this string.
     * @param String|string $s - the string that is concatenated to the end of this string.
     * @return String a String that represents the concatenation of this object's
     * characters followed by the String argument's characters.
     * @example
     * <code>
     * Examples:
     * $str1 = new String('cares');
     * $str1->concat('s') returns 'caress'
     * $str2 = new String('to');
     * $str2->concat('get')->concat('her') returns 'together'
     * </code>
     */
    public function concat($s) {
        return new self($this->str . $s);
    }

    /**
     * Returns true if and only if this string represents the same sequence of
     * characters as the specified sequence.
     * @param String|string $s the sequence to search for. If needle is not a string|String,
     * it is converted to an integer and applied as the ordinal value of a character.
     * @return boolean true if this string contains the specified sequence of char values.
     */
    public function contains($s) {
        if ($s instanceof self) {
            return (strpos($this->str, $s->__toString()) !== false);
        }
        return (strpos($this->str, $s) !== false);
    }

    /**
     * Returns true if and only if this string represents the same sequence of
     * characters as the specified case-insensitive sequence.
     * @param String|string $s the sequence to search for. If needle is not a string|String,
     * it is converted to an integer and applied as the ordinal value of a character.
     * @return boolean true if this string contains the specified case-insensitive sequence of char values.
     */
    public function containsIgnoreCase($s) {
        if ($s instanceof self) {
            return (stripos($this->str, $s->__toString()) !== false);
        }
        return (stripos($this->str, $s) !== false);
    }

    /**
     * Tests if this string ends with the specified suffix.
     * @param String|string $suffix the character sequence to search for
     * @return boolean true if the character sequence represented by the argument is
     * a character
     */
    public function endsWith($suffix) {
        if ($suffix instanceof self) {
            $suffix = $suffix->__toString();
        }
        $beginIndex = strrpos($this->str, strval($suffix));
        $temp = $this->substring($beginIndex);
        return ($temp->equals($suffix));
    }

    /**
     * Tests if this string ends with the specified suffix (case insensitive).
     * @param String|string $suffix the character sequence to search for
     * @return boolean true if the character sequence represented by the argument is
     * a character
     */
    public function endsWithIgnoreCase($suffix) {
        $beginIndex = strripos($this->str, strval($suffix));
        $temp = $this->substring($beginIndex);
        return ($temp->equalsIgnoreCase($suffix));
    }

    /**
     * Compares this string to the specified String. The result is true if and only
     * if the argument is not null and is a String object that represents the same
     * sequence of characters as this object.
     * of characters as this object.
     * @param String|string $s the String to compare this String against.
     * @return boolean true if the Strings are equal; false otherwise.
     */
    public function equals($s) {
        if (!($s instanceof self))
            $s = new self($s);
        return ($this->compareTo($s) == 0);
    }

    /**
     * Compares this String to another String, ignoring case considerations.
     * @param String|string $s the String to compare this String against.
     * @return boolean true if the argument is not null and the Strings are equal,
     * ignoring case; false otherwise.
     */
    public function equalsIgnoreCase($s) {
        if (!($s instanceof self))
            $s = new self($s);
        return ($this->compareToIgnoreCase($s) == 0);
    }

    /**
     * Returns the integer value of this String, using the specified base
     * for the conversion (the default is base 10).
     * @param int $base the base for the conversion
     * @return int|boolean  the integer value of this String on success, or false
     * on failure. If this string is empty returns false, otherwise returns true.
     *
     */
    public function intVal($base = 10) {
        if ($base == 10) {
            return intval($this->str);
        }
        return intval($this->str, $base);
    }

    public function isEmpty() {
        return empty($this->str);
    }

    /**
     * Finds whether this String is numeric. Numeric strings consist of optional
     * sign, any number of digits, optional decimal part and optional exponential
     * part. Thus +0123.45e6 is a valid numeric value. Hexadecimal notation (0xFF)
     * is allowed too but only without sign, decimal and exponential part.
     * @return bool returns true if this object is a number of a numeric string,
     * false otherwise.
     */
    public function isNumber() {
        return intval($this->str);
    }

    /**
     * String is HTML 
     * 
     * @return bool
     */
    public function isHtml() {
        return $this->beginsWith("<!DOCTYPE");
    }

    /**
     * String is XML 
     * 
     * @return bool
     */
    public function isXml() {
        return $this->beginsWith("<?xml");
    }

    /**
     * Strip whitespace (or other characters) from the beginning of a string
     * @param String|string $s the characters you want to strip
     * @return String returns a String with whitespace (or other characters) stripped
     */
    public function leftTrim($s = ' ') {
        return ($s == ' ') ? new self(ltrim($this->str)) : new self(ltrim($this->str, $s));
    }

    /**
     * Strip whitespace (or specified characaters) from the end of a string
     * @param String|string $s the characters you want to strip
     * @return String returns a String with whitespace (or specified characters) stripped
     */
    public function rightTrim($s = ' ') {
        return ($s == ' ') ? new self(rtrim($this->str)) : new self(rtrim($this->str, $s));
    }

    /**
     * Tests if this string starts with the specified prefix beginning at specified index.
     * @param String|string $prefix characters to search
     * @param int $toffset default to zero
     * @return boolean true if this string starts with specified prefix, false otherwise.
     */
    public function startsWith($prefix, $toffset = 0) {
        $temp = substr($this->str, 0, ($this->length() - 1) * -1);
        if (!($prefix instanceof self)) {
            $prefix = new self($prefix);
        }
        $endIndex = $prefix->length();
        $substr = $this->substring(0, $endIndex);
        return $substr->equalsIgnoreCase($prefix);
    }

    /**
     * Returns a hash code for this string.
     * Note: The hash code for a string object is computed as sha1
     * @return String a sha1 hash code value for this object.
     */
    public function hashCode() {
        $hash = hash('sha1', $this->str);
        return new self($hash);
    }

    /**
     * Returns the index within this string of the first occurrence of a
     * case-insensitive string.
     * @param string $needle Characters to search for within this string. Note that
     * the characters may be a string of one or more characters.
     * @param int $offset The optional offset parameter allows you to specify which
     * character in this string to start searching. The position returned is still
     * relative to the beginning of this string.
     * @return int|boolean Index of the first occurrence of the specified character.
     * If string is not found, boolean false will be returned.
     */
    public function indexOf($needle, $offset = 0) {
        return stripos($this->str, $needle, $offset);
    }

    /**
     * Returns the length of this string.
     * @return int the length of the sequence of characters represented by this object.
     */
    public function length() {
        return strlen($this->str);
    }

    /**
     * Tells whether this string matches the given regular
     * <a href="http://www.php.net/manual/en/intro.pcre.php">expression</a>.
     * @param String|string $regex the pattern to search for, as a String.
     * @return boolean true if, and only if, this string matches the given regular expression
     */
    public function matches($regex) {
        return preg_match($regex, $this->str);
    }

    /**
     * Replace all occurrences of the target string with the replacement string
     * @param mixed $target The value being searched for, otherwise known as the
     * needle. An array may be used to designate multiple needles.
     * @param mixed $replacement The replacement value that replaces found target
     * values. An array may be used to designate multiple replacements.
     * @return String the resulting String
     */
    public function replace($target, $replacement) {
        $s = str_replace($target, $replacement, $this->str);
        return new self($s);
    }

    /**
     * Perform a regular expression search and replace
     * @param string|array $regx the regular expression to search for. It can be either a
     * string or an array of strings.
     * @param string|array $replacement The replacement value that replaces found target
     * values. An array may be used to designate multiple replacements.
     * @return String If matches are found, the new String will be found, otherwise String
     * will be returned unchanged or NULL if an error occurred
     */
    public function pregReplace($regx, $replacement) {
        $s = preg_replace($regx, $replacement, $this->str);
        return new self($s);
    }

    /**
     * Replace all occurrences of the target string with the replacement string (ignoring case)
     * @param mixed $target The value being searched for, otherwise known as the
     * needle. An array may be used to designate multiple needles.
     * @param mixed $replacement The replacement value that replaces found target
     * values. An array may be used to designate multiple replacements.
     * @return String the resulting String
     */
    public function replaceIgnoreCase($target, $replacement) {
        $s = str_ireplace($target, $replacement, $this->str);
        return new self($s);
    }

    /**
     * Returns a new String that is a substring of this string. The substring beings
     * at the specified beginIndex and extends to the character at index endIndex - 1.
     * Thus the length of the substring is endIndex-beginIndex.
     * @example Examples:
     * 'hamburger'->substring(4, 8) returns 'urge'
     * 'smiles'->substring(1,5) returns 'mile'
     * @param int $beginIndex - the begining index, inclusive.
     * @param int $endIndex - the ending index, exclusive.
     * @return String the specified substring.
     */
    public function substring($beginIndex, $endIndex = null) {
        if (is_null($endIndex)) {
            $endIndex = $this->length();
        }
        $substr = substr($this->str, $beginIndex, $endIndex);
        return new self($substr);
    }

    /**
     * Converts all of the characters in this string to upper case using the rules
     * of the default local.
     * @return String Returns String with all alphabetic characters converted to uppercase.
     */
    public function toUpperCase() {
        //$s = strtoupper($this->str);
        //$s = mb_strtoupper($this->str, 'utf-8');
        $s = mb_convert_case($this->str, MB_CASE_UPPER, $this->encoding);
        return new self($s);
    }

    /**
     * Converts all of the characters in this string to lower case using the rules
     * of the default local.
     * @return StrBox Returns String with all alphabetic characters converted to lowercase.
     */
    public function toLowerCase() {
        //$s = strtolower($this->str);
        //$s = mb_strtolower($this->str, 'utf-8');
        $s = mb_convert_case($this->str, MB_CASE_LOWER, $this->encoding);
        return new self($s);
    }

    /**
     * Converts all of the characters in this string to upper
     * 
     * @return String Returns String with all alphabetic characters converted to uppercase.
     */
    public function toUpper() {
        $s = strtoupper($this->str);
        return new self($s);
    }

    /**
     * Converts all of the characters in this string to lower
     * 
     * @return StrBox Returns String with all alphabetic characters converted to lowercase.
     */
    public function toLower() {
        $s = strtolower($this->str);
        return new self($s);
    }

    /**
     * Strip whitespace (or other characters) from the beginning and end of a string
     * @param mixed $s the character list to search for.
     * @return String returns the new String object
     */
    public function trim($s = null) {
        if (is_null($s)) {
            return new self(trim($this->str));
        }
        if ($s instanceof self) {
            return new self(trim($this->str, $s->__toString()));
        }
        return new self(trim($this->str, strval($s)));
    }

    /**
     * Truncate modifier
     *
     * Name:     truncate<br>
     * Purpose:  Truncate a string to a certain length if necessary,
     *           optionally splitting in the middle of a word, and
     *           appending the $etc string or inserting $etc into the middle.
     * 
     * @param integer $length
     * @param string $encoding
     * @param string $etc
     * @param boolean $break_words
     * @param boolean $middle
     * 
     * @return string
     */
    public function Truncate($length = 80, $encoding = 'UTF-8', $etc = '...', $break_words = false, $middle = false) {
        if ($length == 0)
            return '';
        $string = $this->str;
        if (strlen($string) > $length) {
            $length -= min($length, strlen($etc));
            if (!$break_words && !$middle) {
                $string = mb_ereg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length + 1, $encoding));
            }
            if (!$middle) {
                $string = mb_substr($string, 0, $length, $encoding) . $etc;
            } else {
                $string = mb_substr($string, 0, $length / 2, $encoding) . $etc . mb_substr($string, -$length / 2, $encoding);
            }
        }
        return new self($string);
    }

    /**
     * Strip modifier
     *
     * Name:     strip<br>
     * Purpose:  Replace all repeated spaces, newlines, tabs
     *           with a single space or supplied replacement string.<br>
     * @param string $replace
     * @param string $encoding
     * 
     * @return Default_Plugin_String
     */
    public function Strip($replace = ' ', $encoding = 'UTF-8') {
        $string = $this->str;
        mb_regex_encoding($encoding);
        $string = mb_ereg_replace('\s+', $replace, $string);
        return new self($string);
    }

    /**
     * Removing all unnecessary html
     *
     *
     * @param string $text
     * @return array
     */
    public function cleanHtml() {
        $retour = array();
        $text = $this->str;
        //--------------------
        //replace blank characters by spaces
        $text = ereg_replace("[\r\n\t]+", " ", $text);

        //extracts title
        if (preg_match('/< *title *>(.*?)< *\/ *title *>/is', $text, $regs)) {
            $title = trim($regs[1]);
        } else {
            $title = "";
        }

        // Transform HTML characters to sequence
        $title = htmlspecialchars_decode($title, ENT_QUOTES);

        //delete content of head, script, and style tags
        $text = eregi_replace("<head[^>]*>.*</head>", " ", $text);
        //$text = eregi_replace("<script[^>]*>.*</script>"," ",$text); // more conservative
        $text = preg_replace("/<script[^>]*?>.*?<\/script>/is", " ", $text); // less conservative
        $text = eregi_replace("<style[^>]*>.*</style>", " ", $text);
        // clean tags
        $text = preg_replace("/<[\/\!]*?[^<>]*?>/is", " ", $text);

        while (eregi('&#([0-9]{3});', $text, $reg)) {
            $text = str_replace($reg[0], chr($reg[1]), $text);
        }
        while (eregi('&#x([a-f0-9]{2});', $text, $reg)) {
            $text = str_replace($reg[0], chr(base_convert($reg[1], 16, 10)), $text);
        }

        //replace foo characters by space
        $text = eregi_replace("[*{}()\"\r\n\t]+", " ", $text);
        $text = eregi_replace("<[^>]*>", " ", $text);
        $text = ereg_replace("(\r|\n|\r\n)", " ", $text);

        // replace any stranglers by space
        $text = eregi_replace("( -> | <- | > | < )", " ", $text);

        //strip characters used in highlighting with no space
        $text = str_replace("^#_", "", str_replace("_#^", "", $text));
        $text = str_replace("@@@", "", str_replace("@#@", "", $text));

        $text = ereg_replace("[[:space:]]+", " ", $text);

        // Transform HTML characters to sequence
        $text = htmlspecialchars_decode($text, ENT_QUOTES);


        $retour['content'] = new self($text);
        $retour['title'] = new self($title);
        return $retour;
    }

    /**
     * Counts the number of words inside string. If the optional format is not
     * specified, then the return value will be an integer representing the number
     * of words found.
     * @param int $format specify the return value of this function. The current
     * values are:<br/>
     * <ul>
     * <li>0 - returns the number of words found</li>
     * <li>1 - returns an array containing all the words found inside the string</li>
     * <li>2 - returns an associative array, where the key is the number position of
     * the word inside the string and the value is the actual word itself</li>
     * </ul>
     * @return int|array returns an array or an integer, depending on the format chosen.
     */
    public function numOfWords($format = 0) {
        return str_word_count($this->str, $format);
    }

    /**
     * Returns a string with the first character of str capitalized, if that character is alphabetic.
     * Note that 'alphabetic' is determined by the current locale. For instance,
     * in the default "C" locale characters such as umlaut-a (ä) will not be converted.
     * @return String the resulting String object
     */
    public function ucfirst() {
        return new self(ucfirst($this->str));
    }

    /**
     * Transform the string in transliteration
     * 
     * @return String the resulting String object
     */
    public function translit() {
        $value = $this->str;
        //----------------
        // Массив символов
        $letters = array(
            "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e",
            "ё" => "e", "ж" => "zh", "з" => "z", "и" => "i", "й" => "j", "к" => "k",
            "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "c",
            "ч" => "ch", "ш" => "sh", "щ" => "sh", "ы" => "i", "ь" => "", "ъ" => "",
            "э" => "e", "ю" => "yu", "я" => "ya",
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D", "Е" => "E",
            "Ё" => "E", "Ж" => "ZH", "З" => "Z", "И" => "I", "Й" => "J", "К" => "K",
            "Л" => "L", "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R",
            "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "C",
            "Ч" => "CH", "Ш" => "SH", "Щ" => "SH", "Ы" => "I", "Ь" => "", "Ъ" => "",
            "Э" => "E", "Ю" => "YU", "Я" => "YA",
        );

        // We pass through the array and replace each character of the filtered values
        foreach ($letters as $letterVal => $letterKey) {
            $value = str_replace($letterVal, $letterKey, $value);
        }

        return new self($value);
    }

    /**
     * Generate CHPU
     *
     * @param string $str
     * @return string
     */
    function generate_chpu() {
        $str = $this->str;
        //----------------
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );
        $str = strtr($str, $converter);
        $str = strtolower($str);
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        $str = trim($str, "-");
        return new self($str);
    }

    /**
     * Split the string into substrings
     * Returns an array of strings obtained from the decomposition of string
     * Using separator as the delimiter.
     * If the argument passed to limit, the array will contain a maximum of limit
     * Elements with the last element containing the rest of string. 
     * 
     * @param string $separator
     * @param int $limit
     * @return array 
     */
    public function toArray($separator, $limit = 0) {
        $arrStrTmp = array();
        //---------------

        if ($separator instanceof self) {
            $separator = $separator->__toString();
        }

        $string = $this->str;

        if ($limit) {
            $arrStr = explode($separator, $string, $limit);
        } else {
            $arrStr = explode($separator, $string);
        }

        // Transform an array of strings in the array of objects
        foreach ($arrStr as $value) {
            $arrStrTmp[] = new self($value);
        }

        return $arrStrTmp;
    }

    /**
     * The function tp link () takes as an argument and returns your text 
     * text has replaced the URL to the active links.
     * 
     * @param string $buf
     * @return String the resulting String object
     */
    function tolink() {
        $buf = $this->str;
        //----------------
        $x = explode(" ", $buf);
        $newbuf = '';
        for ($j = 0; $j < count($x); $j++) {
            if (preg_match
                            ("/(http:\\/\\/)?([a-z_0-9-.]+\\.[a-z]{2,3}(([ \"'>\r\n\t])|(\\/([^ \"'>\r\n\t]*)?)))/", $x[$j], $ok))
                $newbuf.=str_replace($ok[2], "<a href='http://$ok[2]'>$ok[2]</a>", str_replace("http://", "", $x[$j])) . " ";
            else
                $newbuf.=$x[$j] . " ";
        }
        return new self($newbuf);
    }

    /**
     * Compress string format gzip
     * 
     * @param int $level The level of compression. 0 - no compression, 9 - maximum compression. If not specified, the compression level will be used by default library zlib
     * @return StrBox|false The compressed string or FALSE on error
     */
    function gzPack($level = 9) {
        //Compressing content
        $data = $this->str;
        $gzdata = gzencode($data, $level);
        if ($gzdata === FALSE) {
            throw new \Exception("Compression error strings in the gzip format");
        }
        return new self($gzdata);
    }

    /**
     * Decompresses a string in the format gzip
     * 
     * @return StrBox|false Uncompressed data or FALSE on error
     */
    function gzUnpack() {
        //Decompressing content
        $gzdata = $this->str;
        $data = gzinflate(substr($gzdata, 10, -8));
        if ($data === FALSE) {
            throw new \Exception("Unpacking error strings in the gzip format");
        }
        return new self($data);
    }

    /**
     * Compresses a string in the format ZLIB
     * 
     * @param int $level The level of compression. 0 - no compression, 9 - maximum compression. If not specified, will use the default compression level of the zlib library
     * @return StrBox|false Compressed data, or FALSE on error
     */
    function gzCompress($level = 9) {
        //Compressing content
        $data = $this->str;
        $gzcompress = gzcompress($data, $level);
        if ($gzcompress === FALSE) {
            throw new \Exception("Compression error strings in the ZLIB format");
        }
        return new self($gzcompress);
    }

    /**
     * Uncompresses a string in the format ZLIB
     * 
     * @param int $length The maximum size of the data to decompress
     * @return StrBox|false Uncompressed data, or FALSE on error
     */
    function gzUncompress($length = 0) {
        //Decompressing content
        $gzcompress = $this->str;
        $data = gzuncompress($gzcompress, $length);
        if ($data === FALSE) {
            throw new \Exception("Uncompression error strings in the ZLIB format");
        }
        return new self($data);
    }

    /**
     * Compresses a string in the format DEFLATE
     * 
     * @param int $level The level of compression. 0 - no compression, 9 - maximum compression. If not specified, will use the default compression level of the zlib library
     * @return StrBox|false Compressed data, or FALSE on error
     */
    function gzDeflate($level = 9) {
        //Compressing content
        $data = $this->str;
        $gzdeflate = gzdeflate($data, $level);
        if ($gzdeflate === FALSE) {
            throw new \Exception("Compression error strings in the DEFLATE format");
        }
        return new self($gzdeflate);
    }

    /**
     * Uncompresses a string in the format DEFLATE
     * 
     * @param int $length The maximum size of the data to decompress
     * @return StrBox|false Uncompressed data, or FALSE on error
     */
    function gzInflate($length = 0) {
        //Decompressing content
        $gzdeflate = $this->str;
        $data = gzinflate($gzdeflate, $length);
        if ($data === FALSE) {
            throw new \Exception("Uncompression error strings in the DEFLATE format");
        }
        return new self($data);
    }

    /**
     * It encodes the data algorithm MIME base64
     * 
     * @return StrBox|false The encoded data, or FALSE on error
     */
    function base64Encode() {
        $data = $this->str;
        $encode = base64_encode($data);
        if ($encode === FALSE) {
            throw new \Exception("Encode error strings in the base64 format");
        }
        return new self($encode);
    }

    /**
     * It decodes the data algorithm MIME base64
     * 
     * @return StrBox|false The decoded data, or FALSE on error
     */
    function base64Decode() {
        //Decompressing content
        $encode = $this->str;
        $decode = base64_decode($encode);
        if ($decode === FALSE) {
            throw new \Exception("Decode error strings in the base64 format");
        }
        return new self($decode);
    }

    /**
     * Code for making GUIDs
     * guid = {b1eaa119-3fb0-a9f2-4381-762fb4c464bb}
     * 
     * @return StrBox
     */
    function getGUID() {
        if (function_exists('com_create_guid')) {
            $uuid = com_create_guid();
        } else {
            mt_srand((double) microtime() * 10000); //optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid = chr(123)// "{"
                    . substr($charid, 0, 8) . $hyphen
                    . substr($charid, 8, 4) . $hyphen
                    . substr($charid, 12, 4) . $hyphen
                    . substr($charid, 16, 4) . $hyphen
                    . substr($charid, 20, 12)
                    . chr(125); // "}"
        }
        return new self($uuid);
    }

    /**
     * Code for valid GUIDs
     * guid = {b1eaa119-3fb0-a9f2-4381-762fb4c464bb}
     * 
     * @return boolean
     */
    function validGUID() {
        $guid = $this->str;
        if (preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $guid)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Escapes a text for HTML.
     *
     * @param int    $flags        The flags (@see htmlspecialchars)
     * @param bool   $doubleEncode When double_encode is turned off PHP will not encode existing html entities, the default is to convert everything.
     *
     * @return StrBox
     */
    function escape($flags = ENT_COMPAT, $doubleEncode = true)
    {
        $str = htmlspecialchars($this->str, $flags, $this->encoding, $doubleEncode);
        return new self($str);
    }
    
    /**
     * Unescapes a text for HTML.
     *
     * @param int    $flags        The flags (@see htmlspecialchars)
     * @return StrBox
     */
    function unEscape($flags = ENT_COMPAT)
    {
        $str = htmlspecialchars_decode($this->str, $flags);
        return new self($str);
    }
}

/**
 * @author Azeem Michael
 * This interface imposes a total ordering on the objects of each class that implements
 * it. This ordering is referred to as the class's natural ordering, and the class's
 * compareTo method is referred to as its natural comparison method
 */
interface ComparableInterface {

    /**
     * Compares this object with the specified object for order. Returns a negative
     * integer, zero, or a positive integer as this object is less than, equal to,
     * or greater than the specified object
     * 
     * @uses Compares this object with the specified object for order
     * @param Comparable $o - the object to be compared
     * @return int a negative integer, zero, or a positive integer as this object is
     * less than, equal to, or greater than the specified object
     */
    public function compareTo(ComparableInterface $o);

    /**
     * Case Insensitive Comparator
     * @param Comparable $o - the object to be compared
     * @return int a negative integer, zero, or a positive integer as this object is
     * less than, equal to, or greater than the specified object
     */
    public function compareToIgnoreCase(ComparableInterface $o);
}
