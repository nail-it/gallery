export default function(text, type) {
    $("#alert").attr('class', 'bg-' + type);
    $("#alert").html(text);
    $("#alert").show().delay(5000).fadeOut();
}