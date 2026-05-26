function generarHV() {
    let nombre = document.getElementById("nombre").value;
    let heroe = document.getElementById("heroe").value;
    let cargo = document.getElementById("cargo").value;
    let descripcion = document.getElementById("descripcion").value;
    let habilidades = document.getElementById("habilidades").value;
    let experiencia = document.getElementById("experiencia").value;
    let educacion = document.getElementById("educacion").value;

    let agente = {
    nombre:      nombre,
    heroe:       heroe,
    cargo:       cargo,
    descripcion: descripcion,
    habilidades: habilidades,
    experiencia: experiencia,
    educacion:   educacion
};

    document.getElementById("hv-nombre").innerHTML = agente.nombre;
    document.getElementById("hv-heroe").innerHTML = "Heroe de inspiracion: " + agente.heroe;
    document.getElementById("hv-cargo").innerHTML = agente.cargo;
    document.getElementById("hv-descripcion").innerHTML = agente.descripcion;
    document.getElementById("hv-habilidades").innerHTML = agente.habilidades;
    document.getElementById("hv-experiencia").innerHTML = agente.experiencia;
    document.getElementById("hv-educacion").innerHTML = agente.educacion;

    document.getElementById("formulario").style.display = "none";
    document.getElementById("hoja").style.display = "block";

}

function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    html2canvas(document.getElementById("contenido-hv")).then(function(canvas) {
        let imagen = canvas.toDataURL("image/png");
        doc.addImage(imagen, "PNG", 10,10,190,0);
        doc.save("HojaDEVida_SHIELD.pdf");
    });
}

function volver() {
    document.getElementById("hoja").style.display = "none";
    document.getElementById("formulario").style.display = "block";
}