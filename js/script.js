document.addEventListener("DOMContentLoaded", function () {

    // Mensaje de aviso: usa SweetAlert si está disponible, si no alert()
    function avisar(texto, campo) {
        if (window.Swal) {
            Swal.fire({
                icon: "warning",
                title: "Revisa los datos",
                text: texto,
                confirmButtonColor: "#0f2a43",
                confirmButtonText: "Entendido"
            }).then(function () {
                if (campo) { campo.focus(); }
            });
        } else {
            alert(texto);
            if (campo) { campo.focus(); }
        }
    }

    // ---------- Validaciones del formulario de hotel (registrar y editar) ----------
    document.querySelectorAll("#formHotel").forEach(function (formulario) {
        formulario.addEventListener("submit", function (evento) {
            const nombre = formulario.querySelector("#nombre");
            const ciudad = formulario.querySelector("#ciudad");
            const direccion = formulario.querySelector("#direccion");
            const habitaciones = formulario.querySelector("#habitaciones");
            const precio = formulario.querySelector("#precio");

            if (!nombre.value.trim() || !ciudad.value.trim() || !direccion.value.trim()) {
                evento.preventDefault();
                avisar("Completa todos los campos de texto.", nombre);
                return;
            }

            if (habitaciones.value === "" ||
                !Number.isInteger(Number(habitaciones.value)) ||
                Number(habitaciones.value) < 1) {
                evento.preventDefault();
                avisar("El número de habitaciones debe ser un entero mayor que 0.", habitaciones);
                return;
            }

            if (!Number.isFinite(Number(precio.value)) || Number(precio.value) <= 0) {
                evento.preventDefault();
                avisar("El precio debe ser un número mayor que 0.", precio);
                return;
            }

            // Datos válidos: pedir confirmación antes de enviar
            evento.preventDefault();
            const esEdicion = formulario.querySelector("input[name='id']") !== null;

            if (!window.Swal) {
                if (confirm(esEdicion ? "¿Guardar los cambios?" : "¿Guardar el nuevo hotel?")) {
                    formulario.submit();
                }
                return;
            }

            Swal.fire({
                icon: "question",
                title: esEdicion ? "¿Guardar cambios?" : "¿Registrar hotel?",
                html: (esEdicion ? "Se actualizará " : "Se registrará ") + "<strong></strong>.",
                showCancelButton: true,
                confirmButtonText: esEdicion ? "Sí, guardar cambios" : "Sí, registrar",
                cancelButtonText: "Revisar",
                confirmButtonColor: "#0f2a43",
                cancelButtonColor: "#64748b",
                reverseButtons: true,
                didOpen: function (popup) {
                    popup.querySelector("strong").textContent = nombre.value.trim();
                }
            }).then(function (resultado) {
                if (resultado.isConfirmed) {
                    Swal.fire({
                        title: "Guardando...",
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: function () { Swal.showLoading(); }
                    });
                    formulario.submit();
                }
            });
        });
    });

    // ---------- Confirmación antes de eliminar (SweetAlert) ----------
    document.querySelectorAll(".form-eliminar").forEach(function (formulario) {
        formulario.addEventListener("submit", function (evento) {
            evento.preventDefault();
            const nombre = formulario.dataset.nombre || "este hotel";

            if (!window.Swal) {
                if (confirm("¿Eliminar el hotel \"" + nombre + "\"? Esta acción no se puede deshacer.")) {
                    formulario.submit();
                }
                return;
            }

            Swal.fire({
                icon: "warning",
                title: "¿Eliminar hotel?",
                html: "Se eliminará <strong></strong> de forma permanente.<br>Esta acción no se puede deshacer.",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#b42318",
                cancelButtonColor: "#64748b",
                reverseButtons: true,
                focusCancel: true,
                didOpen: function (popup) {
                    // textContent evita inyectar HTML desde el nombre del hotel
                    popup.querySelector("strong").textContent = nombre;
                }
            }).then(function (resultado) {
                if (resultado.isConfirmed) {
                    formulario.submit();
                }
            });
        });
    });

    // ---------- Resultado de la operación (agregado / actualizado / eliminado / error) ----------
    const parametros = new URLSearchParams(window.location.search);
    const mensajes = {
        agregado:    { icon: "success", title: "¡Hotel registrado!",    text: "El hotel se guardó correctamente." },
        actualizado: { icon: "success", title: "¡Hotel actualizado!",   text: "Los cambios se guardaron correctamente." },
        eliminado:   { icon: "success", title: "¡Hotel eliminado!",     text: "El hotel fue eliminado correctamente." },
        error:       { icon: "error",   title: "No se pudo completar",  text: "Ocurrió un problema con la operación." }
    };
    const resultadoOp = mensajes[parametros.get("mensaje")];

    if (resultadoOp && window.Swal) {
        // SweetAlert reemplaza al aviso en pantalla para no mostrarlo dos veces
        document.querySelectorAll(".alerta").forEach(function (a) { a.style.display = "none"; });
        Swal.fire({
            icon: resultadoOp.icon,
            title: resultadoOp.title,
            text: resultadoOp.text,
            confirmButtonColor: "#0f2a43",
            confirmButtonText: "Aceptar",
            timer: resultadoOp.icon === "success" ? 3000 : undefined,
            timerProgressBar: true
        });
        // Quitar ?mensaje= de la URL para que al recargar no se repita
        if (window.history.replaceState) {
            window.history.replaceState({}, "", window.location.pathname);
        }
    }

    // ---------- Las alertas de éxito se ocultan solas ----------
    document.querySelectorAll(".alerta.exito").forEach(function (alerta) {
        setTimeout(function () {
            alerta.classList.add("oculta");
        }, 4500);
    });
});