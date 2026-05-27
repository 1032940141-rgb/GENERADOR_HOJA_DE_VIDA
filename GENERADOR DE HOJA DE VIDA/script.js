// Variables contadoras - Taller 5 Punto 1 y 2
let eC = 0;
let dC = 0;
let cC = 0;
let lC = 0;
let rC = 0;

// Arreglo de porcentajes para habilidades - Taller 5 Punto 21
let porcentajes = [95, 85, 78, 70, 88, 75, 92, 68];

// Objeto para niveles de idioma - Taller 5 Punto 18
let nivelPuntos = {
    Basico: 1,
    Intermedio: 2,
    Avanzado: 4,
    Nativo: 5
};

// Funcion para obtener valor de un campo - Taller 5 Punto 15/17
function obtenerValor(id) {
    let el = document.getElementById(id);
    if (el) {
        return el.value;
    } else {
        return "";
    }
}

// Funcion para eliminar un bloque - Taller 5 Punto 15
function rm(id) {
    let el = document.getElementById(id);
    if (el) {
        el.remove();
    }
}

// Funcion para previsualizar la foto - Taller 5 Punto 17
function previewFoto(e) {
    let file = e.target.files[0];
    if (!file) {
        return;
    }
    let r = new FileReader();
    r.onload = function(ev) {
        window._foto = ev.target.result;
        document.getElementById("photo-preview").innerHTML = '<img src="' + ev.target.result + '" style="width:100%;height:100%;object-fit:cover;" />';
    };
    r.readAsDataURL(file);
}

// Funcion agregar experiencia - Taller 5 Punto 16/17
function addExp() {
    let i = eC;
    eC = eC + 1;
    let bloque = document.createElement("div");
    bloque.className = "dyn-block";
    bloque.id = "exp-" + i;
    bloque.innerHTML =
        '<button class="rm-btn" onclick="rm(\'exp-' + i + '\')">✕</button>' +
        '<div class="form-row">' +
        '<div class="field"><label>Cargo / Puesto</label><input type="text" id="ec-' + i + '" placeholder="Ej: Analista de Sistemas" /></div>' +
        '<div class="field"><label>Empresa</label><input type="text" id="ee-' + i + '" placeholder="Ej: Empresa XYZ S.A.S." /></div>' +
        '</div>' +
        '<div class="form-row">' +
        '<div class="field"><label>Fecha inicio</label><input type="text" id="ei-' + i + '" placeholder="Ej: Enero 2021" /></div>' +
        '<div class="field"><label>Fecha fin</label><input type="text" id="ef-' + i + '" placeholder="Presente" /></div>' +
        '</div>' +
        '<div class="form-row">' +
        '<div class="field"><label>Ciudad</label><input type="text" id="ecd-' + i + '" placeholder="Bogota, Colombia" /></div>' +
        '<div class="field"><label>Modalidad</label><select id="em-' + i + '"><option value="">—</option><option>Presencial</option><option>Remoto</option><option>Hibrido</option></select></div>' +
        '</div>' +
        '<div class="field"><label>Funciones y logros</label><textarea id="ed-' + i + '" style="min-height:90px;" placeholder="Describa responsabilidades y logros clave..."></textarea></div>';
    document.getElementById("exp-container").appendChild(bloque);
}

// Funcion agregar educacion
function addEdu() {
    let i = dC;
    dC = dC + 1;
    let bloque = document.createElement("div");
    bloque.className = "dyn-block";
    bloque.id = "edu-" + i;
    bloque.innerHTML =
        '<button class="rm-btn" onclick="rm(\'edu-' + i + '\')">✕</button>' +
        '<div class="form-row">' +
        '<div class="field"><label>Titulo obtenido</label><input type="text" id="dt-' + i + '" placeholder="Ej: Ingenieria Industrial" /></div>' +
        '<div class="field"><label>Nivel academico</label><select id="dn-' + i + '"><option value="">—</option><option>Bachillerato</option><option>Tecnico</option><option>Tecnologo</option><option>Pregrado</option><option>Especializacion</option><option>Maestria</option><option>Doctorado</option></select></div>' +
        '</div>' +
        '<div class="form-row">' +
        '<div class="field"><label>Institucion</label><input type="text" id="di-' + i + '" placeholder="Ej: Universidad Nacional de Colombia" /></div>' +
        '<div class="field"><label>Año de grado</label><input type="text" id="da-' + i + '" placeholder="Ej: 2018" /></div>' +
        '</div>' +
        '<div class="field"><label>Ciudad donde estudio</label><input type="text" id="dci-' + i + '" placeholder="Ej: Bogota, Colombia" /></div>';
    document.getElementById("edu-container").appendChild(bloque);
}

// Funcion agregar certificacion
function addCert() {
    let i = cC;
    cC = cC + 1;
    let bloque = document.createElement("div");
    bloque.className = "dyn-block";
    bloque.id = "cert-" + i;
    bloque.innerHTML =
        '<button class="rm-btn" onclick="rm(\'cert-' + i + '\')">✕</button>' +
        '<div class="form-row">' +
        '<div class="field"><label>Nombre del certificado</label><input type="text" id="cn-' + i + '" placeholder="Ej: AWS Certified" /></div>' +
        '<div class="field"><label>Institucion emisora</label><input type="text" id="ci-' + i + '" placeholder="Ej: Amazon Web Services" /></div>' +
        '</div>' +
        '<div class="form-row">' +
        '<div class="field"><label>Año</label><input type="text" id="ca-' + i + '" placeholder="2023" /></div>' +
        '<div class="field"><label>Validez</label><input type="text" id="cv2-' + i + '" placeholder="Ej: 2026 o Sin vencimiento" /></div>' +
        '</div>';
    document.getElementById("cert-container").appendChild(bloque);
}

// Funcion agregar idioma
function addLang() {
    let i = lC;
    lC = lC + 1;
    let bloque = document.createElement("div");
    bloque.className = "dyn-block";
    bloque.id = "lang-" + i;
    bloque.innerHTML =
        '<button class="rm-btn" onclick="rm(\'lang-' + i + '\')">✕</button>' +
        '<div class="form-row">' +
        '<div class="field"><label>Idioma</label><input type="text" id="ln-' + i + '" placeholder="Ej: Ingles" /></div>' +
        '<div class="field"><label>Nivel</label><select id="ll-' + i + '"><option>Basico</option><option>Intermedio</option><option>Avanzado</option><option>Nativo</option></select></div>' +
        '</div>';
    document.getElementById("lang-container").appendChild(bloque);
}

// Funcion agregar referencia
function addRef() {
    let i = rC;
    rC = rC + 1;
    let bloque = document.createElement("div");
    bloque.className = "dyn-block";
    bloque.id = "ref-" + i;
    bloque.innerHTML =
        '<button class="rm-btn" onclick="rm(\'ref-' + i + '\')">✕</button>' +
        '<div class="form-row">' +
        '<div class="field"><label>Nombre completo</label><input type="text" id="rn-' + i + '" placeholder="Ej: Carlos Rodriguez" /></div>' +
        '<div class="field"><label>Cargo y empresa</label><input type="text" id="rc-' + i + '" placeholder="Ej: Gerente · Empresa ABC" /></div>' +
        '</div>' +
        '<div class="form-row">' +
        '<div class="field"><label>Telefono</label><input type="text" id="rt-' + i + '" placeholder="+57 310 000 0000" /></div>' +
        '<div class="field"><label>Correo</label><input type="text" id="re-' + i + '" placeholder="ref@correo.com" /></div>' +
        '</div>';
    document.getElementById("ref-container").appendChild(bloque);
}

// Funcion renderizar barras de habilidades - Taller 5 Punto 22 (recorrer arreglo con for)
function renderSkills(contenedorId, csv) {
    let el = document.getElementById(contenedorId);
    el.innerHTML = "";
    let habilidades = csv.split(",");
    for (let i = 0; i < habilidades.length; i++) {
        let sk = habilidades[i];
        if (sk != "") {
            let porcentaje = porcentajes[i % porcentajes.length];
            el.innerHTML = el.innerHTML + '<div class="skill-item"><div class="skill-name">' + sk + '</div><div class="skill-bar"><div class="skill-fill" style="width:' + porcentaje + '%"></div></div></div>';
        }
    }
}

// Funcion principal generar CV - Taller 5 Punto 25 (integrar HTML + JS)
function generar() {

    document.getElementById("loader").classList.add("on");

    // Objeto con datos del profesional - Taller 5 Punto 18
    let persona = {
        nombre:       obtenerValor("f-nombre"),
        cargo:        obtenerValor("f-cargo"),
        nacimiento:   obtenerValor("f-nacimiento"),
        nacionalidad: obtenerValor("f-nacionalidad"),
        cedula:       obtenerValor("f-cedula"),
        estado:       obtenerValor("f-estado"),
        email:        obtenerValor("f-email"),
        telefono:     obtenerValor("f-telefono"),
        ciudad:       obtenerValor("f-ciudad"),
        perfil:       obtenerValor("f-perfil"),
        objetivo:     obtenerValor("f-objetivo")
    };

    // Validar si el nombre esta vacio - Taller 5 Punto 8 (IF)
    let nombreFinal = "";
    if (persona.nombre != "") {
        nombreFinal = persona.nombre;
    } else {
        nombreFinal = "SU NOMBRE";
    }

    // Validar si el cargo esta vacio
    let cargoFinal = "";
    if (persona.cargo != "") {
        cargoFinal = persona.cargo;
    } else {
        cargoFinal = "Profesional";
    }

    setTimeout(function() {
        document.getElementById("loader").classList.remove("on");

        // Mostrar foto - IF simple Taller 5 Punto 8
        let foto = document.getElementById("cv-foto");
        if (window._foto) {
            foto.src = window._foto;
            foto.style.display = "block";
        } else {
            foto.style.display = "none";
        }

        // Mostrar nombre y cargo - Taller 5 Punto 19/20 (mostrar y modificar propiedad)
        document.getElementById("cv-nombre").textContent = nombreFinal.toUpperCase();
        document.getElementById("cv-cargo").textContent = cargoFinal;
        document.getElementById("cv-footer").textContent = nombreFinal + " · Hoja de vida";

        // Armar datos personales con arreglo - Taller 5 Punto 21
        let datArr = [];
        if (persona.nacimiento != "") {
            datArr.push("Nac: " + persona.nacimiento);
        }
        if (persona.nacionalidad != "") {
            datArr.push(persona.nacionalidad);
        }
        if (persona.cedula != "") {
            datArr.push(persona.cedula);
        }
        if (persona.estado != "") {
            datArr.push(persona.estado);
        }
        // Recorrer arreglo con for - Taller 5 Punto 22
        let textoDatos = "";
        for (let i = 0; i < datArr.length; i++) {
            if (i == 0) {
                textoDatos = datArr[i];
            } else {
                textoDatos = textoDatos + "  ·  " + datArr[i];
            }
        }
        document.getElementById("cv-datos").textContent = textoDatos;

        // Contacto con switch para el icono - Taller 5 Punto 10 (SWITCH)
        let contactoHtml = "";
        let camposContacto = ["email", "telefono", "ciudad"];
        for (let i = 0; i < camposContacto.length; i++) {
            let campo = camposContacto[i];
            let valor = "";
            // IF anidado para obtener el valor - Taller 5 Punto 9
            if (campo == "email") {
                valor = persona.email;
            } else {
                if (campo == "telefono") {
                    valor = persona.telefono;
                } else {
                    valor = persona.ciudad;
                }
            }
            if (valor != "") {
                let icono = "";
                // Switch para asignar icono - Taller 5 Punto 10
                switch (campo) {
                    case "email":
                        icono = "✉";
                        break;
                    case "telefono":
                        icono = "📞";
                        break;
                    case "ciudad":
                        icono = "📍";
                        break;
                    default:
                        icono = "·";
                        break;
                }
                contactoHtml = contactoHtml + '<div class="doc-contact-item">' + icono + ' <span>' + valor + '</span></div>';
            }
        }
        document.getElementById("cv-contacto").innerHTML = contactoHtml;

        // Perfil y objetivo
        document.getElementById("cv-perfil").textContent = persona.perfil;
        let elObj = document.getElementById("cv-objetivo");
        elObj.textContent = persona.objetivo;
        if (persona.objetivo != "") {
            elObj.style.display = "";
        } else {
            elObj.style.display = "none";
        }

        // Experiencias con FOR - Taller 5 Punto 11
        let expList = document.getElementById("cv-exp-list");
        expList.innerHTML = "";
        for (let i = 0; i < eC; i++) {
            if (!document.getElementById("exp-" + i)) {
                continue;
            }
            let cargo  = obtenerValor("ec-" + i);
            let emp    = obtenerValor("ee-" + i);
            let ini    = obtenerValor("ei-" + i);
            let fin    = obtenerValor("ef-" + i);
            let ciu    = obtenerValor("ecd-" + i);
            let mod    = obtenerValor("em-" + i);
            let desc   = obtenerValor("ed-" + i);

            if (cargo == "" && emp == "") {
                continue;
            }

            // Operador logico && - Taller 5 Punto 7
            let fecha = "";
            if (ini != "" && fin != "") {
                fecha = ini + " – " + fin;
            } else {
                if (ini != "") {
                    fecha = ini;
                } else {
                    fecha = fin;
                }
            }

            let lugar = "";
            if (emp != "" && ciu != "" && mod != "") {
                lugar = emp + " · " + ciu + " · " + mod;
            } else {
                if (emp != "" && ciu != "") {
                    lugar = emp + " · " + ciu;
                } else {
                    if (emp != "") {
                        lugar = emp;
                    } else {
                        lugar = ciu;
                    }
                }
            }

            let fechaHtml = "";
            if (fecha != "") {
                fechaHtml = '<div class="exp-date">' + fecha + '</div>';
            }
            let descHtml = "";
            if (desc != "") {
                descHtml = '<div class="exp-desc">' + desc + '</div>';
            }

            expList.innerHTML = expList.innerHTML +
                '<div class="exp-item">' +
                '<div class="exp-header"><div class="exp-title">' + cargo + '</div>' + fechaHtml + '</div>' +
                '<div class="exp-place">' + lugar + '</div>' +
                descHtml +
                '</div>';
        }

        // Educacion con WHILE - Taller 5 Punto 13
        let eduList = document.getElementById("cv-edu-list");
        eduList.innerHTML = "";
        let i = 0;
        while (i < dC) {
            if (document.getElementById("edu-" + i)) {
                let tit = obtenerValor("dt-" + i);
                let niv = obtenerValor("dn-" + i);
                let ins = obtenerValor("di-" + i);
                let ano = obtenerValor("da-" + i);
                let ciu = obtenerValor("dci-" + i);
                if (tit != "" || ins != "") {
                    let sub = "";
                    if (niv != "" && ins != "") {
                        sub = niv + " · " + ins;
                    } else {
                        if (niv != "") {
                            sub = niv;
                        } else {
                            sub = ins;
                        }
                    }
                    if (ciu != "") {
                        sub = sub + " · " + ciu;
                    }
                    let anoHtml = "";
                    if (ano != "") {
                        anoHtml = '<div class="edu-year">' + ano + '</div>';
                    }
                    eduList.innerHTML = eduList.innerHTML +
                        '<div class="edu-item">' +
                        '<div class="edu-left"><div class="edu-title">' + tit + '</div><div class="edu-sub">' + sub + '</div></div>' +
                        anoHtml +
                        '</div>';
                }
            }
            i = i + 1;
        }

        // Certificaciones con DO WHILE - Taller 5 Punto 14
        let certList = document.getElementById("cv-cert-list");
        certList.innerHTML = "";
        let hayCert = false;
        let j = 0;
        if (cC > 0) {
            do {
                if (document.getElementById("cert-" + j)) {
                    let nom = obtenerValor("cn-" + j);
                    let ins = obtenerValor("ci-" + j);
                    let ano = obtenerValor("ca-" + j);
                    let val = obtenerValor("cv2-" + j);
                    if (nom != "") {
                        hayCert = true;
                        let sub = "";
                        if (ins != "") {
                            sub = ins;
                        }
                        if (ano != "") {
                            sub = sub + " · " + ano;
                        }
                        if (val != "") {
                            sub = sub + " · Valido hasta " + val;
                        }
                        let subHtml = "";
                        if (sub != "") {
                            subHtml = " — " + sub;
                        }
                        certList.innerHTML = certList.innerHTML +
                            '<div class="cert-item">' +
                            '<div class="cert-dot"></div>' +
                            '<div class="cert-text"><strong>' + nom + '</strong>' + subHtml + '</div>' +
                            '</div>';
                    }
                }
                j = j + 1;
            } while (j < cC);
        }
        // Operador logico - mostrar u ocultar seccion
        if (hayCert) {
            document.getElementById("sec-cert").style.display = "";
        } else {
            document.getElementById("sec-cert").style.display = "none";
        }

        // Habilidades con IF - Taller 5 Punto 8
        let htec = obtenerValor("f-htec");
        if (htec != "") {
            renderSkills("cv-htec", htec);
            document.getElementById("sec-htec").style.display = "";
        } else {
            document.getElementById("sec-htec").style.display = "none";
        }

        let hbland = obtenerValor("f-hbland");
        if (hbland != "") {
            renderSkills("cv-hbland", hbland);
            document.getElementById("sec-hbland").style.display = "";
        } else {
            document.getElementById("sec-hbland").style.display = "none";
        }

        // Idiomas con FOR + objeto nivelPuntos - Taller 5 Punto 11 + 19
        let idList = document.getElementById("cv-idiomas");
        idList.innerHTML = "";
        for (let k = 0; k < lC; k++) {
            if (!document.getElementById("lang-" + k)) {
                continue;
            }
            let nom = obtenerValor("ln-" + k);
            let niv = obtenerValor("ll-" + k);
            if (niv == "") {
                niv = "Basico";
            }
            if (nom == "") {
                continue;
            }
            // Acceder a propiedad del objeto - Taller 5 Punto 19
            let pts = nivelPuntos[niv];
            if (!pts) {
                pts = 3;
            }
            let dots = "";
            // FOR para los puntos de nivel - Taller 5 Punto 11
            for (let d = 1; d <= 5; d++) {
                if (d <= pts) {
                    dots = dots + '<div class="lang-dot filled"></div>';
                } else {
                    dots = dots + '<div class="lang-dot"></div>';
                }
            }
            idList.innerHTML = idList.innerHTML +
                '<div class="lang-item">' +
                '<div class="lang-label"><span>' + nom + '</span><span class="lang-level">' + niv + '</span></div>' +
                '<div class="lang-dots">' + dots + '</div>' +
                '</div>';
        }
        if (idList.innerHTML == "") {
            idList.innerHTML = '<p style="font-size:.72rem;color:#999;">No especificado</p>';
        }

        // Referencias con FOR + IF anidado - Taller 5 Punto 9
        let refList = document.getElementById("cv-refs");
        refList.innerHTML = "";
        let hayRef = false;
        for (let r = 0; r < rC; r++) {
            if (!document.getElementById("ref-" + r)) {
                continue;
            }
            let nom = obtenerValor("rn-" + r);
            let car = obtenerValor("rc-" + r);
            let tel = obtenerValor("rt-" + r);
            let eml = obtenerValor("re-" + r);
            if (nom != "") {
                hayRef = true;
                let carHtml = "";
                let telHtml = "";
                let emlHtml = "";
                if (car != "") {
                    carHtml = '<div class="ref-role">' + car + '</div>';
                }
                if (tel != "") {
                    telHtml = '<div class="ref-contact">📞 ' + tel + '</div>';
                }
                if (eml != "") {
                    emlHtml = '<div class="ref-contact">✉ ' + eml + '</div>';
                }
                refList.innerHTML = refList.innerHTML +
                    '<div class="ref-item">' +
                    '<div class="ref-name">' + nom + '</div>' +
                    carHtml + telHtml + emlHtml +
                    '</div>';
            }
        }

        // IF anidado para referencias - Taller 5 Punto 9
        if (!hayRef) {
            let checkSolicitud = document.getElementById("f-ref-solicitud");
            if (checkSolicitud.checked) {
                refList.innerHTML = '<div class="side-item"><strong>Disponibles</strong>A solicitud del empleador</div>';
            } else {
                document.getElementById("sec-refs").style.display = "none";
            }
        }

        // Mostrar pagina CV - Taller 5 Punto 25
        document.getElementById("form-page").style.display = "none";
        document.getElementById("cv-page").style.display = "block";
        window.scrollTo(0, 0);

    }, 900);
}

// Funcion volver al formulario - Taller 5 Punto 15
function volver() {
    document.getElementById("cv-page").style.display = "none";
    document.getElementById("form-page").style.display = "flex";
    window.scrollTo(0, 0);
}

// Inicializar un bloque de cada seccion al cargar
if (!window._cvInitialized) {
    window._cvInitialized = true;
    addExp();
    addEdu();
    addCert();
    addLang();
    addRef();
}
