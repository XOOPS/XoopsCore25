function showHideHelp() {
    $(".xoform-help").toggle();
}

function xoopsExternalLinks() {
    if (!document.getElementsByTagName) return;
    var anchors = document.getElementsByTagName("a");
    for (var i = 0; i < anchors.length; i++) {
        var anchor = anchors[i];
        if (anchor.getAttribute("href")) {
            // Check rel value with extra rels, like "external noflow". No test for performance yet
            $pattern = new RegExp("external", "i");
            if ($pattern.test(anchor.getAttribute("rel"))) {
                anchor.target = "_blank";
            }
        }
    }
}

function xoopsGetElementById(id) {
    return $(id);
}

function selectModule(id, button) {

    if (button.value == 1) {
        $('#'+id).css('background-color', '#ebf0ff');
    } else {
        $('#'+id).css('background-color', 'transparent');
    }
}

function showThemeSelected() {
    $(".theme_preview").hide();
    var theme = '#' + $("#theme_set").val();
    $(theme).show();
}

function passwordStrength(password) {

    var score = zxcvbn(password).score;

    document.getElementById("passwordDescription").innerHTML = desc[score];
    document.getElementById("passwordStrength").className = "strength" + score;
}

function suggestPassword(passwordlength) {
    var pwchars = "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ.,:";
    var pwchars = "abcdefhjmnpqrstuvwxyz1234567890,?;.:!$=+@_-&|#ABCDEFGHJKLMNPQRSTUVWYXZ";
    var passwd = document.getElementById('generated_pw');
    passwd.value = '';

    for (i = 0; i < passwordlength; i++) {
        passwd.value += pwchars.charAt(Math.floor(Math.random() * pwchars.length))
    }
    return passwd.value;
}


/**
 * Copy the generated password (or anything in the field) to the form
 *
 * @return  boolean  always true
 */
function suggestPasswordCopy() {
    var pw = $('#generated_pw');
    var generated_pw = pw.val();

    $('#adminpass').val(generated_pw);
    $('#adminpass2').val(generated_pw);

    passwordStrength(generated_pw);
    return true;
}

window.onload = xoopsExternalLinks;
