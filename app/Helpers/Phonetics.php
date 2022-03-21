<?php
declare(strict_types=1);
    if (!function_exists('textWord')) {
        function textWord(string $s1 = '', string $s2 = ''): string
        {
            if ($s1 <> $s2)
                return (string)("{$s1}{$s2}");
            else
                return (string)$s1;

        }
    }

    if (!function_exists('removeAcents')) {
        function removeAcents(string $word): array
        {
            $lowercaseWord = strtolower(Trim($word));
            $lowercaseWord = preg_replace(array("/(á|à|ã|â|ä)/","/(é|è|ê|ë)/","/(í|ì|î|ï)/","/(ó|ò|õ|ô|ö)/","/(ú|ù|û|ü)/","/(ñ)/","/(')/"), explode(" ", "a e i o u n "), $lowercaseWord);
            return explode(" ", $lowercaseWord);
        }
    }

    if(!function_exists('position')) {
        function position(int $max = 0, int $pos = 0): int
        {
            if ($pos < 0)
                return 0;
            elseif ($pos < $max)
                return $pos;
            else
                return $max;
        }
    }

/**
 * Phonetic Generator
 * Developed by
 * Dalton Rogerio Alvarez
 * https://www.linkedin.com/in/daltonralv
 *  */
function phonetics($textWord): string
{
    if ((is_null($textWord)) || (trim($textWord) == ''))
        return '';



    $vstr = [];
    $V = ['a', 'e', 'i', 'o', 'u', 'y', 'á', 'é', 'ê', 'ó', 'ô', 'í', 'ú', 'â', 'à', 'ä', 'è', 'ë', 'ì', 'ï', 'î', 'ò', 'ö', 'ü', 'ù', 'û'];
    $V1 = ['e', 'i', 'y', 'é', 'ê', 'í', 'è', 'ë', 'ì', 'ï', 'î', 'ù', 'û'];
    $C = ['b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'q', 'r', 's', 't', 'v', 'w', 'x', 'z'];
    $max = (int)0;
    $textWordNoAccent = removeAcents($textWord);
    $word = [];

    foreach ($textWordNoAccent as $word) {
        $max = strlen($word) - 1;
        $n = (int)-1;
        $L =  (string)'';
        $L1 = (string)'';
        $L2 = (string)'';
        $L3 = (string)'';
        if ($word <> '') {
            do {
                $n = position($max, $n + 1);
                $L = $word[$n];
                if (in_array($L, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']))
                    array_push($vstr, $L);

                elseif (($n < $max) && ($word[$n] == $word[$n + 1]))
                    $n = position($max, $n + 1);
                if (in_array($L, ['a' , 'á' , 'â', 'à', 'ä', 'ã']))
                    array_push($vstr, 'a');
                elseif (in_array($L, ['e', 'è', 'ë', 'é' ,'ê']))
                    array_push($vstr, 'e');

                elseif (in_array($L, ['i' , 'y' , 'í' , 'ì' , 'ï' , 'î'])){
                    if (!((in_array($word[position($max, $n - 1)], ['e', 'é'])) && (in_array($word[position($max, $n + 1)], ['a', 'o']))))
                        array_push($vstr, 'i');

                }elseif (in_array($L, ['o' , 'ò' , 'ö', 'õ'])) {
                    if (($max == $n) || ($word[position($max, $n + 2)] == 's'))
                        array_push($vstr, 'u');
                    else
                        array_push($vstr, 'o');

                }elseif (in_array($L, ['ó' , 'ô']))
                    array_push($vstr, 'o');
                elseif (in_array($L, ['u' , 'ú', 'ü', 'ù', 'û']))
                    array_push($vstr, 'u');
                elseif (in_array($L, ['b' , 'f' , 'j' , 'k' , 'v']))
                    array_push($vstr, $word[$n]);
                elseif ($L == 'c'){
                    $L1 = $word[position($max, $n + 1)];
                    $L2 = textWord($word[position($max, $n + 1)], $word[position($max, $n + 2)]);
                    if (in_array($L1, $V1))
                        array_push($vstr, 's');

                    elseif (in_array($L1, ['a', 'o', 'u', 'r', 'l']))
                        array_push($vstr, 'k');

                    elseif ($L2 == 'hr') {  //christina, chrizóstemo
                        array_push($vstr, 'kR');
                        $n = position($max, $n + 2);

                    }elseif ($L1 == 'h') {
                        array_push($vstr, 'x');
                        $n = position($max, $n + 1);

                    }elseif ($L1 == 'k') {
                        array_push($vstr, 'k');
                        $n = position($max, $n + 1);

                    }else
                        array_push($vstr, 'k');

                }elseif ($L == 'd') {
                    $L1 = $word[position($max, $n + 1)];
                    if ((in_array($L1, $C)) && (!(in_array($L1, ['r', 'l']))) || ($max == $n))
                        array_push($vstr, 'di');
                    else
                        array_push($vstr, 'd');

                }elseif ($L == 'g') {
                    $L1 = $word[position($max, $n + 1)];
                    $L2 = textWord($word[position($max, $n + 1)], $word[position($max, $n + 2)]);
                    $L3 = $word[position($max, $n - 2)];
                    if (($L2 == 'ue') || ($L2 == 'ui') || ($L1 == 'ü')) {
                        array_push($vstr, 'g');
                        $n = position($max, $n + 1);

                    }elseif (in_array($L1, ['i', 'e']))
                        array_push($vstr, 'j');
                    elseif (($L3 == 'i') && ($L1 == 'n')) {
                        array_push($vstr, 'n');
                        $n = position($max, $n + 1);

                    }else
                        array_push($vstr, 'g');

                }elseif ($L == 'l') {
                    $L1 = $word[position($max, $n + 1)];
                    if ($L1 == 'h') {
                        array_push($vstr, 'L');
                        $n = position($max, $n + 1);

                    }elseif ($max == $n)
                        array_push($vstr, 'u');
                    elseif (in_array($L1, $C))
                        array_push($vstr, 'u');
                    else
                        array_push($vstr, 'l');

                }elseif ($L == 'm') {
                    $L1 = $word[position($max, $n + 1)];
                    $L2 = $word[position($max, $n - 1)];
                    if ((in_array($L2, $V)) && (in_array($L1, $C)) || ($max == $n))
                        array_push($vstr, 'n');
                    else
                        array_push($vstr, 'm');

                }elseif ($L == 'n') {
                    $L1 = $word[position($max, $n + 1)];
                    if ($L1 == 'h') {
                        array_push($vstr, 'N');
                        $n = position($max, $n + 1);
                    } else
                        array_push($vstr, 'n');

                }elseif ($L == 'p') {
                    $L1 = $word[position($max, $n + 1)];
                    if ($L1 == 'h') {
                        array_push($vstr, 'f');
                        $n = position($max, $n + 1);
                    }else
                        array_push($vstr, 'p');

                }elseif ($L == 'q') {
                    $L1 = $word[position($max, $n + 1)];
                    $L2 = textWord($word[position($max, $n + 1)], $word[position($max, $n + 2)]);
                    if (($L2 == 'ue') || ($L2 == 'ui')) {
                        array_push($vstr, 'k');
                        $n = position($max, $n + 1);
                    }else
                        array_push($vstr, 'k');

                }elseif ($L == 'r') {
                    $L1 = $word[position($max, $n - 1)];
                    if (in_array($L1, [' ', 'n', 'm', 'r']))
                        array_push($vstr, 'r');
                    else
                        array_push($vstr, 'R');

                }elseif ($L == 's') {
                    $L1 = $word[position($max, $n + 1)];
                    $L2 = textWord($word[position($max, $n + 1)], $word[position($max, $n + 2)]);
                    $L3 = $word[position($max, $n - 1)];
                    if ($L1 == 'h') {
                        array_push($vstr, 'x');
                        $n = position($max, $n + 1);
                    }elseif (($n == 0) && (in_array($L1, $V)))
                        array_push($vstr, 's');
                    elseif (($n == 0) && (in_array($L1, $C)))
                        array_push($vstr, 'es');
                    elseif (($L2 == 'ce') || ($L2 == 'ci') || ($L1 == 'ç')) {
                        array_push($vstr, 's');
                        $n = position($max, $n + 1);
                    }elseif ((in_array($L3, $V)) && (in_array($L1, $V)))
                        array_push($vstr, 'z');
                    elseif ((in_array($L3, $V)) && (in_array($L1, $C)))
                        array_push($vstr, 's');
                    elseif (($L2 == 'ex') && (in_array($L3, $V)))
                        array_push($vstr, 'z');
                    elseif ((in_array($L3, $C)) && (in_array($L1, $V)))
                        array_push($vstr, 's');
                    else
                        array_push($vstr, 's');

                }elseif ($L == 't') {
                    $L1 = $word[position($max, $n + 1)];
                    if ($L1 == 'h')
                        array_push($vstr, 'te');
                    elseif ($max == $n)
                        array_push($vstr, 't');
                    else
                        array_push($vstr, 't');

                } elseif ($L == 'w') {
                    $L2 = textWord($word[position($max, $n + 1)], $word[position($max, $n + 2)]);
                    if (($L2 == 'al') || ($L2 == 'an'))
                        array_push($vstr, 'v');
                    else
                        array_push($vstr, 'u');
                } elseif ($L == 'x') {
                    $L1 =  $word[position($max, $n + 1)];
                    $L2 = textWord($word[position($max, $n + 1)], $word[position($max, $n + 2)]);
                    $L3 =  $word[position($max, $n - 1)];
                    if (($n == 0) || ($L3 == 'n'))
                        array_push($vstr, 'x');
                    elseif (($L2 == 'ce') || ($L2 == 'ci')) {
                        array_push($vstr, 's');
                        $n = position($max, $n + 1);
                    }elseif ((in_array($L3, $V)) && ($L1 == 't'))
                        array_push($vstr, 's');
                    elseif (($L3 == 'e') && (in_array($L1, $V)))
                        array_push($vstr, 'z');
                    else
                        array_push($vstr, 'x');

                } elseif ($L == 'z') {
                    $L1 = $word[position($max, $n + 1)];
                    if ($n == 0)
                        array_push($vstr, 'z');
                    elseif (($max == $n) || (in_array($L1, $C)))
                        array_push($vstr, 's');
                    else
                        array_push($vstr, 'z');

                } elseif ((Ord($L) == 195) || ($L == 'ç'))
                    array_push($vstr, 's');

            } while ($n < $max);
        }
    }
    return implode($vstr);
}
