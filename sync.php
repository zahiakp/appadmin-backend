<?php

include './inc/head.php';
include './inc/const.php';
include './inc/db.php';

$programs=[
    [
      "id"=> "12",
      "name"=> "POEM URDU",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "23",
      "name"=> "DISCUSSION URDU",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "30",
      "name"=> "QAWWALI",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "5",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "32",
      "name"=> "LECTURING ENGLISH",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "43",
      "name"=> "MASTER PLAN",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "46",
      "name"=> "KITHABIC RESEARCH (BOOK WRITING ARABIC)",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "51",
      "name"=> "BLURB WRITING ENGLISH",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "56",
      "name"=> "POEM ARABIC",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "67",
      "name"=> "ESSAY ARABIC",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "69",
      "name"=> "KATHAPRASANGAM MALAYALAM",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "4",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "70",
      "name"=> "RESEARCH PAPER ENGLISH",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "74",
      "name"=> "NEWS BULLETIN",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "83",
      "name"=> "STORY MALAYALAM",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "85",
      "name"=> "SPOKEN TRANSLATION MAL-ARB",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "86",
      "name"=> "IBARATH READING",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "92",
      "name"=> "HIFZUL MUTHOON",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "95",
      "name"=> "BOOK CRITICISM",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "98",
      "name"=> "STORY URDU",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "104",
      "name"=> "RESEARCH POSTER PRESENTATION",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "106",
      "name"=> "SPOKEN TRANSLATION MAL-ENG",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "108",
      "name"=> "JOURNAL REVIEW",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "112",
      "name"=> "BALAGA TEST",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "120",
      "name"=> "BOOK REVIEW",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "123",
      "name"=> "SHARIA TALK (ELOCUTION ARABIC)",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "125",
      "name"=> "MAS’ALA SOLUTION (IFTHA’)",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "127",
      "name"=> "POEM ENGLISH",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "131",
      "name"=> "TALENT TEST",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "136",
      "name"=> "HADITH TRANSLATION",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "138",
      "name"=> "DOCUMENTRY PRESENTATION",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "139",
      "name"=> "ELOCUTION MALAYALAM",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "147",
      "name"=> "ELOCUTION ENGLISH",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "148",
      "name"=> "STORY ARABIC",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "149",
      "name"=> "WRITTEN TRANSLATION MAL-URD",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "152",
      "name"=> "CALLIGRAFFITI",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "153",
      "name"=> "ESSAY ENGLISH",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "156",
      "name"=> "ESSAY URDU",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "163",
      "name"=> "PHILOSOPHICAL SLICE",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "164",
      "name"=> "SHARH WRITING (SHARHUL MUTHOON)",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "166",
      "name"=> "STORY ENGLISH",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "168",
      "name"=> "TRANSLATION DIALOGUE",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "5",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "169",
      "name"=> "MADH SONG",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "170",
      "name"=> "SOP WRITING",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "174",
      "name"=> "CHANNEL DISCUSSION",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "5",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "182",
      "name"=> "QASEEDA",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "5",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "183",
      "name"=> "ARCHITECTURAL PHOTOGRAPHY",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "186",
      "name"=> "ESSAY MALAYALAM",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "191",
      "name"=> "ELOCUTION URDU",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "195",
      "name"=> "POEM MALAYALAM",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "197",
      "name"=> "MAPPILAPPATTU RACHANA",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "200",
      "name"=> "TRILINGUAL TYPING",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "215",
      "name"=> "QUIZ",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "218",
      "name"=> "IDEAL DIALOGUE",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "219",
      "name"=> "GLOBAL DARS",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "220",
      "name"=> "PAPER PRESENTATION ENGLISH",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "221",
      "name"=> "BRANDING",
      "category"=> "senior",
      "isStage"=> "0",
      "isGroup"=> "1",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "222",
      "name"=> "TARTEEL",
      "category"=> "senior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> null
    ],
    [
      "id"=> "223",
      "name"=> "POEM MALAYALAM",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "224",
      "name"=> "MADH SONG WRITING",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "225",
      "name"=> "MICROPHOTOGRAPHY",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "226",
      "name"=> "FLASH FICTION ENGLISH",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "227",
      "name"=> "IMLA’",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "228",
      "name"=> "SWARF TEST",
      "category"=> "minor",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "229",
      "name"=> "SOCIAL TWEET MALAYALAM",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "230",
      "name"=> "STORY ENGLISH",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "231",
      "name"=> "WORD GAME ARABIC (PADHAKKALARI)",
      "category"=> "minor",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "232",
      "name"=> "STORY ENGLISH",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "233",
      "name"=> "STORY ENGLISH",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "234",
      "name"=> "ELOCUTION MALAYALAM",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "235",
      "name"=> "HIFZUL MUTHOON",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "236",
      "name"=> "ESSAY MALAYALAM",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "237",
      "name"=> "FEATURE WRITING",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "238",
      "name"=> "KITHABIC TEST",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "239",
      "name"=> " BALAGA TEST",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "240",
      "name"=> "DIARY WRITING HINDI",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "ongoing",
      "order"=> "0"
    ],
    [
      "id"=> "241",
      "name"=> "TARTEEL",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "242",
      "name"=> "DICTIONARY MAKING ARB- ENG",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "243",
      "name"=> "SWARF TEST",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "244",
      "name"=> "POEM ENGLISH",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "245",
      "name"=> "ELOCUTION MALAYALAM",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "246",
      "name"=> "WRITTEN TRANSLATION  ARABI MAL - MAL",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "247",
      "name"=> "VOCABULARY ARABIC",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "248",
      "name"=> "REEL CREATION",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "249",
      "name"=> "ELOCUTION ARABIC",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "250",
      "name"=> "POEM ENGLISH",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "251",
      "name"=> "STORY ARABIC",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "252",
      "name"=> "SPOKEN TRANSLATION MAL-ENG",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "253",
      "name"=> "BOOK CRITICISM",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "254",
      "name"=> "WRITTEN TRANSLATION MAL-URD",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "255",
      "name"=> "POEM ARABIC",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "256",
      "name"=> "CAMPUS SONG",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "5",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "257",
      "name"=> "CAPTION WRITING",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "258",
      "name"=> "STORY MALAYALAM",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "259",
      "name"=> "CALLIGRAPHY",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "260",
      "name"=> "HIFZUL QURAN",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "261",
      "name"=> "SOCIAL TWEET ENGLISH",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "262",
      "name"=> "TARTEEL",
      "category"=> "minor",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "263",
      "name"=> "SPOKEN TRANSLATION MAL-ARB",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "264",
      "name"=> "HANDWRITING ENGLISH",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "265",
      "name"=> "QUIZ",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "266",
      "name"=> "STORY MALAYALAM",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "ongoing",
      "order"=> "0"
    ],
    [
      "id"=> "267",
      "name"=> "HIFZUL MUTHOON",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "268",
      "name"=> "CASE STUDY & SURVEY",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "1",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "269",
      "name"=> "BOOK TEST",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "270",
      "name"=> "IMLA’",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "271",
      "name"=> "MADH SONG",
      "category"=> "minor",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "272",
      "name"=> "POEM MALAYALAM",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "273",
      "name"=> "ESSAY ARABIC",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "274",
      "name"=> "DOCUMENTARY PRESENTATION",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "275",
      "name"=> "DEVOTIONAL SONG",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "276",
      "name"=> "RISALA CREATION (BOOK WRITING ARABIC)",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "277",
      "name"=> "BOOK REVIEW",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "278",
      "name"=> "QUIZ",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "279",
      "name"=> "AD MAKING",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "280",
      "name"=> "NA’AT",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "4",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "281",
      "name"=> "PYGMY POEM ENGLISH",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "ongoing",
      "order"=> "0"
    ],
    [
      "id"=> "282",
      "name"=> "PYGMY POEM URDU",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "283",
      "name"=> "SUFI VIRUTHAM",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "284",
      "name"=> "ESSAY URDU",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "285",
      "name"=> "DIGITAL DRAWING",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "286",
      "name"=> "CARTOON SCAPE",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "287",
      "name"=> "IBARATH READING",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "288",
      "name"=> "TALK MASTER ARABIC",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "289",
      "name"=> "PUBLIC TALK",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "290",
      "name"=> "IBARATH READING",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "291",
      "name"=> "NASHEEDHA",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "5",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "292",
      "name"=> "WATERCOLORING",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "ongoing",
      "order"=> "0"
    ],
    [
      "id"=> "293",
      "name"=> "FIQH TRANSLATION  ARA- ENG",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "294",
      "name"=> "WATERCOLORING",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "295",
      "name"=> "WRITTEN TRANSLATION  MAL- ENG",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "296",
      "name"=> "PYGMY POEM ARABIC",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "297",
      "name"=> "ESSAY ENGLISH",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "298",
      "name"=> "BOOK TEST",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "ongoing",
      "order"=> "0"
    ],
    [
      "id"=> "299",
      "name"=> "KITHABIC TEST",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "300",
      "name"=> "ESSAY MALAYALAM",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "ongoing",
      "order"=> "0"
    ],
    [
      "id"=> "301",
      "name"=> "TED TALK",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "302",
      "name"=> "BOOK TEST",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "303",
      "name"=> "POEM ARABIC",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "304",
      "name"=> "HIFZUL QUR’AN",
      "category"=> "minor",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "305",
      "name"=> "ESSAY MALAYALAM",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "306",
      "name"=> "LIVE EXTEMPORE",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "307",
      "name"=> "TARTEEL",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "308",
      "name"=> "HIFZUL QURAN",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "309",
      "name"=> "ESSAY ARABIC",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "310",
      "name"=> "TARTEEL",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "311",
      "name"=> "ELOCUTION MALAYALAM",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "312",
      "name"=> "TALK MASTER ARABIC",
      "category"=> "minor",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "313",
      "name"=> "POEM URDU",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "314",
      "name"=> "WRITTEN TRANSLATION ENG - MAL",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "315",
      "name"=> "PROMPT CREATION",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "316",
      "name"=> "COLLOQUIUM",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "317",
      "name"=> "IMLA’",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "318",
      "name"=> "SWARF TEST",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "319",
      "name"=> "POEM ENGLISH",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "320",
      "name"=> "ESSAY ENGLISH",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "ongoing",
      "order"=> "0"
    ],
    [
      "id"=> "321",
      "name"=> "HIFZUL MUTHOON",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "322",
      "name"=> "ELOCUTION ARABIC",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "323",
      "name"=> "ESSAY URDU",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "324",
      "name"=> "NAHV TEST",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "325",
      "name"=> "STORY ARABIC",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "326",
      "name"=> "ELOCUTION MALAYALAM",
      "category"=> "minor",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "327",
      "name"=> "GAZAL",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "328",
      "name"=> "POEM MALAYALAM",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "ongoing",
      "order"=> "0"
    ],
    [
      "id"=> "329",
      "name"=> "CALLIGRAFFITI",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "330",
      "name"=> "VA’ALU",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "331",
      "name"=> "HANDWRITING ENGLISH",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "332",
      "name"=> "QUIZ",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "333",
      "name"=> "IBARATH READING",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "334",
      "name"=> "TALENT TEST",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "335",
      "name"=> "ELOCUTION URDU",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "336",
      "name"=> "POEM URDU",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "337",
      "name"=> "ESSAY ENGLISH",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "338",
      "name"=> "STORY URDU",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "339",
      "name"=> "PHILOSOPHICAL SLICE",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "340",
      "name"=> "ESSAY MALAYALAM",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "341",
      "name"=> "STORY URDU",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "342",
      "name"=> "SONG ARABIC",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "343",
      "name"=> "ARABIC KATHAPRASANGAM",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "4",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "344",
      "name"=> "QUIZ",
      "category"=> "minor",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "345",
      "name"=> "SHARHUL MUTHOON",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "346",
      "name"=> "STORY MALAYALAM",
      "category"=> "subjunior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "347",
      "name"=> "TALK MASTER ENGLISH",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "348",
      "name"=> "NAHV TEST",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "349",
      "name"=> "ALFIYA TEST",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "350",
      "name"=> "POEM MALAYALAM",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "351",
      "name"=> "ESSAY ENGLISH",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "352",
      "name"=> "JALSA ARABIC",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "353",
      "name"=> "STORY MALAYALAM",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "354",
      "name"=> "ELOCUTION ENGLISH",
      "category"=> "minor",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "355",
      "name"=> "GROUP SONG",
      "category"=> "minor",
      "isStage"=> "1",
      "isGroup"=> "1",
      "limit"=> "4",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "356",
      "name"=> "IBARATH READING",
      "category"=> "minor",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "357",
      "name"=> "STORY ARABIC",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "ongoing",
      "order"=> "0"
    ],
    [
      "id"=> "358",
      "name"=> "WRITTEN TRANSLATION ENG-MAL",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "ongoing",
      "order"=> "0"
    ],
    [
      "id"=> "359",
      "name"=> "GRAMMAR MASTERY",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "ongoing",
      "order"=> "0"
    ],
    [
      "id"=> "360",
      "name"=> "MATHS RELAY",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "1",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "361",
      "name"=> "SUDOKU",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "ongoing",
      "order"=> "0"
    ],
    [
      "id"=> "362",
      "name"=> "PAPER PRESENTATION ENGLISH",
      "category"=> "premier",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "363",
      "name"=> "SUDOKU",
      "category"=> "premier",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "364",
      "name"=> "SHAIRI KALAM URDU",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "365",
      "name"=> "PAPER PRESENTATION ENGLISH",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "366",
      "name"=> "WORD GAME ARABIC (PADHAKKALARI)",
      "category"=> "subjunior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "1",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "367",
      "name"=> "MAPPILAPPATTU",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "368",
      "name"=> "PAPER PRESENTATION ENGLISH",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "369",
      "name"=> "NEWS READING",
      "category"=> "junior",
      "isStage"=> "1",
      "isGroup"=> "0",
      "limit"=> "2",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "370",
      "name"=> "ABSTRACT WRITING",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "371",
      "name"=> "PODCAST",
      "category"=> "junior",
      "isStage"=> "0",
      "isGroup"=> "1",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ],
    [
      "id"=> "372",
      "name"=> "HANDWRITING ENGLISH",
      "category"=> "minor",
      "isStage"=> "0",
      "isGroup"=> "0",
      "limit"=> "3",
      "status"=> "pending",
      "order"=> "0"
    ]
  ]
  ;
  foreach($programs as $prgrm){
    $sql = "INSERT INTO `schedule` (`program`) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $prgrm['id']);
    $stmt->execute();
  }