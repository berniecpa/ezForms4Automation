/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.15
 * @author Baluart E.I.R.L.
 * * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */
$( document ).ready(function() {

    /**
     * Bootstrap Star Rating
     *
     * Display a Star Rating widget on any field with the '.rating' css class
     *
     * We can display a theme with the following data attributes:
     *
     * 'data-theme=krajee-fas': Font Awesome 5.x Theme
     * 'data-theme=krajee-uni': Unicode Theme
     *
     * We can display the widget in another language with the data-language attribute,
     * for example:
     *
     * 'data-language=es' Shows the widget in spanish
     *
     * @link https://plugins.krajee.com/star-rating
     */

    var ratingFasEl = $('[data-theme="krajee-fas"]');
    var ratingUniEl = $('[data-theme="krajee-uni"]');
    var ratingLanguage = $('[data-language]');

    // Default Theme
    $.when(
        $('head')
            .append('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-star-rating@4.1.2/css/star-rating.min.css" type="text/css" />')
            .append('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-star-rating@4.1.2/themes/krajee-svg/theme.min.css" type="text/css" />')
            .append('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-star-rating@4.1.2/themes/krajee-fas/theme.min.css" type="text/css" />')
            .append('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-star-rating@4.1.2/themes/krajee-uni/theme.min.css" type="text/css" />'),
        $.getScript( "https://cdn.jsdelivr.net/npm/bootstrap-star-rating@4.1.2/js/star-rating.min.js" ),
        $.Deferred(function( deferred ){
            $( deferred.resolve );
        })
    ).done(function () {
        // Font Awesome 5.x Theme
        if (ratingFasEl.length) {
            $.fn.ratingThemes['krajee-fas'] = {
                filledStar: '<i class="fas fa-star"></i>',
                emptyStar: '<i class="far fa-star"></i>',
                clearButton: '<i class="fas fa-minus-circle"></i>'
            };
        }
        // Unicode Theme
        if (ratingUniEl.length) {
            $.fn.ratingThemes['krajee-uni'] = {
                filledStar: '&#x2605;',
                emptyStar: '&#x2606;',
                clearButton: '&#x229d;'
            };
        }
        // Locales
        if (ratingLanguage.length) {
            $.fn.ratingLocales.ar={defaultCaption:"{rating} نجوم",starCaptions:{.5:"نصف نجمة",1:"نجمة واحدة",1.5:"نجمة ونصف",2:"نجمتين",2.5:"نجمتين ونصف",3:"ثلاث نجمات",3.5:"ثلاث نجمات ونصف",4:"أربع نجمات",4.5:"أربع نجمات ونصف",5:"خمسة نجمات"},clearButtonTitle:"مسح",clearCaption:"غير مصنّف"};
            $.fn.ratingLocales.bn={defaultCaption:"{rating} তারা",starCaptions:{.5:"অর্ধেক তারা",1:"এক তারা",1.5:"দেড় তারা",2:"দুই তারা",2.5:"আড়াই তারা",3:"তিন তারা",3.5:"সাড়ে তিন তারা",4:"চার তারা",4.5:"সাড়ে চার তারা",5:"পাঁচ তারা"},clearButtonTitle:"মুছে ফেলুন",clearCaption:"কোন তারা নেই"};
            $.fn.ratingLocales.de={defaultCaption:"{rating} Sterne",starCaptions:{.5:"Halber Stern",1:"Ein Stern",1.5:"Eineinhalb Sterne",2:"Zwei Sterne",2.5:"Zweieinhalb Sterne",3:"Drei Sterne",3.5:"Dreieinhalb Sterne",4:"Vier Sterne",4.5:"Viereinhalb Sterne",5:"Fünf Sterne"},clearButtonTitle:"Zurücksetzen",clearCaption:"Nicht bewertet"};
            $.fn.ratingLocales.es={defaultCaption:"{rating} Estrellas",starCaptions:{.5:"Media Estrella",1:"Una Estrella",1.5:"Una Estrella y Media",2:"Dos Estrellas",2.5:"Dos Estrellas y Media",3:"Tres Estrellas",3.5:"Tres Estrellas y Media",4:"Cuatro Estrellas",4.5:"Cuatro Estrellas y Media",5:"Cinco Estrellas"},clearButtonTitle:"Limpiar",clearCaption:"Sin Calificar"};
            $.fn.ratingLocales.fa={defaultCaption:"{rating} ستاره",starCaptions:{.5:"نیم ستاره",1:"یک ستاره",1.5:"یک و نیم ستاره",2:"دو ستاره",2.5:"دو و نیم ستاره",3:"سه ستاره",3.5:"سه و نیم ستاره",4:"چهار ستاره",4.5:"چهار ستاره",5:"پنج ستاره"},clearButtonTitle:"پاک کردن",clearCaption:"بدون رای"};
            $.fn.ratingLocales.fr={defaultCaption:"{rating} étoiles",starCaptions:{.5:"Une demi étoile",1:"Une étoile",1.5:"Une étoile et demi",2:"Deux étoiles",2.5:"Deux étoiles et demi",3:"Trois étoiles",3.5:"Trois étoiles et demi",4:"Quatre étoiles",4.5:"Quatre étoiles et demi",5:"Cinq étoiles"},clearButtonTitle:"Effacer",clearCaption:"Non noté"};
            $.fn.ratingLocales.gr={defaultCaption:"{rating} Αστέρια",starCaptions:{.5:"Μισό Αστέρι",1:"Ένα Αστέρι",1.5:"Ένα Αστέρι και Μισό",2:"Δύο Αστέρια",2.5:"Δύο Αστέρια και Μισό",3:"Τρία Αστέρια",3.5:"Τρία Αστέρια και Μισό",4:"Τέσσερα Αστέρια",4.5:"Τέσσερα Αστέρια και Μισό",5:"Πέντε Αστέρια"},clearButtonTitle:"Καθαρισμός",clearCaption:"Χωρίς Βαθμολογία"};
            $.fn.ratingLocales.it={defaultCaption:"{rating} Stelle",starCaptions:{.5:"Mezza Stella",1:"Una Stella",1.5:"Una Stella & Mezzo",2:"Due Stelle",2.5:"Due Stelle & Mezzo",3:"Tre Stelle",3.5:"Tre Stelle & Mezzo",4:"Quattro Stelle",4.5:"Quattro Stelle & Mezzo",5:"Cinque Stelle"},clearButtonTitle:"Rimuovi",clearCaption:"Nessuna valutazione"};
            $.fn.ratingLocales.kk={defaultCaption:"{rating} Жұлдыз",starCaptions:{.5:"Жарты жұлдыз",1:"Бір жұлдыз",1.5:"Бір жарым жұлдыз",2:"Екі жұлдыз",2.5:"Екі жарым жұлдыз",3:"Үш жұлдыз",3.5:"Үш жарым жұлдыз",4:"Төрт жұлдыз",4.5:"Төрт жарым жұлдыз",5:"Бес жұлдыз"},clearButtonTitle:"Өшіру",clearCaption:"Рейтингсіз"};
            $.fn.ratingLocales.ko={defaultCaption:"{rating} 별점",starCaptions:{.5:"0.5 점",1:"1 점",1.5:"1.5 점",2:"2 점",2.5:"2.5 점",3:"3 점",3.5:"3.5 점",4:"4 점",4.5:"4.5 점",5:"5 점"},clearButtonTitle:"초기화",clearCaption:"평점 없음"};
            $.fn.ratingLocales.pl={defaultCaption:"{rating} Gwiazdek",starCaptions:{.5:"Pół Gwiazdki",1:"Jedna Gwiazdka",1.5:"Półtora Gwiazdek",2:"Dwie Gwiazdki",2.5:"Dwa i pół Gwiazdek",3:"Trzy Gwiazdki",3.5:"Trzy i pół Gwiazdek",4:"Cztery Gwiazdki",4.5:"Cztery i pół Gwiazdek",5:"Pięć Gwiazdek"},clearButtonTitle:"Powrót",clearCaption:"Nie Oceniać"};
            $.fn.ratingLocales["pt-BR"]={defaultCaption:"{rating} Estrelas",starCaptions:{.5:"Meia Estrela",1:"Uma Estrela",1.5:"Uma Estrela e Meia",2:"Duas Estrelas",2.5:"Duas Estrelas e Meia",3:"Três Estrelas",3.5:"Três Estrelas e Meia",4:"Quatro Estrelas",4.5:"Quatro Estrelas e Meia",5:"Cinco Estrelas"},clearButtonTitle:"Limpar",clearCaption:"Não Avaliado"};
            $.fn.ratingLocales.ro={defaultCaption:"{rating} stele",starCaptions:{.5:"Jumatate de stea",1:"O Stea",1.5:"O stea si jumatate",2:"Doua stele",2.5:"Doua stele si jumatate",3:"Trei stele",3.5:"Trei stele si jumatate",4:"Patru stele",4.5:"Patru stele si jumatate",5:"Cinci stele"},clearButtonTitle:"Sterge",clearCaption:"Fara vot"};
            $.fn.ratingLocales.ru={defaultCaption:"{rating} Звёзды",starCaptions:{.5:"Половина звезды",1:"Одна звезда",1.5:"Полторы звезды",2:"Две звезды",2.5:"Две с половиной звезды",3:"Три звезды",3.5:"Три с половиной звезды",4:"Четыре звезды",4.5:"Четыре с половиной звезды",5:"Пять звёзд"},clearButtonTitle:"Очистить",clearCaption:"Без рейтинга"};
            $.fn.ratingLocales.tr={defaultCaption:"{rating} Yıldız",starCaptions:{.5:"Yarım Yıldız",1:"Tek Yıldız",1.5:"Bir Buçuk Yıldız",2:"İki Yıldız",2.5:"İki Buçuk Yıldız",3:"Üç Yıldız",3.5:"Üç Buçuk Yıldız",4:"Dört Yıldız",4.5:"Dört Buçuk Yıldız",5:"Beş Yıldız"},clearButtonTitle:"Temizle",clearCaption:"Oylanmamış"};
            $.fn.ratingLocales.ua={defaultCaption:"{rating} Зірки",starCaptions:{.5:"Пів зірки",1:"Одна зірка",1.5:"Півтори зірки",2:"Дві зірки",2.5:"Дві з половиною зірки",3:"Три зірки",3.5:"Три з половиною зірки",4:"Чотири зірки",4.5:"Чотири з половиною зірки",5:"П'ять зірок"},clearButtonTitle:"Очистити",clearCaption:"Без рейтингу"};
            $.fn.ratingLocales.zh={defaultCaption:"{rating} 星",starCaptions:{.5:"半星",1:"一星",1.5:"一星半",2:"二星",2.5:"二星半",3:"三星",3.5:"三星半",4:"四星",4.5:"四星半",5:"五星"},clearButtonTitle:"清除",clearCaption:"未评级"};
        }
    });
});
