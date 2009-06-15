function reverse(string) {
    var i = string.length;
    i=i-1;
    result = "";

    for (var x = i; x >=0; x--) {
        result = result + string.charAt(x);
    }
    return result;
}

function mask(type, field) {
    if (type=='amount') {
        seperator = ',';
        number    = 3;
        doReverse   = true;
    } else if (type=='pan') {
        seperator = '-';
        number    = 4;
        doReverse = false;
    }
    search   = new RegExp( seperator, 'ig' );
    original = field.value.replace(search, '');
    if ( doReverse ) {
        original = reverse( original );
    }
    i        = 0;
    result   = "";
    while( i <= original.length ) {
        result = result + original.substr(i,number) + seperator;
        i = i + number;
    }
    if ( doReverse ) {
        result = reverse( result );
    }
    while (result.charAt(0) == seperator) {
        result = result.substr(1);
    }
    while (result.charAt(result.length-1) == seperator) {
        result = result.substr(0,result.length-1);
    }
    field.value = result;
}