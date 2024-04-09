(function () {
  obtenerTareas();
  let tareas = [];
  let filtradas = [];
  let filtroEstado = '';
  // let filtroId = '';

  // Boton para mostrar el modal de nueva tarea
  const nuevaTareaBtn = document.querySelector("#agregar-tarea");
  nuevaTareaBtn.addEventListener("click", () => mostrarFormulario());

  // Filtros de busquedas
  const filtros = document.querySelectorAll('#filtros input[type="radio"]');
  filtros.forEach( radio => {
    radio.addEventListener('input', filtrarTareas);
  })

  function filtrarTareas(e) {
    filtroEstado = e.target.value;
    // filtroId = e.target.id;

    if(filtroEstado !== ''){
      filtradas = tareas.filter(tarea => tarea.estado === filtroEstado);
    } else {
      filtradas = [];
    }

    mostrarTareas();
  }

  async function obtenerTareas() {
    try {
      const id = obtenerProyecto();
      const url = `/api/tareas?id=${id}`;
      const respuesta = await fetch(url);
      const resultado = await respuesta.json();

      tareas = resultado.tareas;
      mostrarTareas();
    } catch (error) {
      console.log(error);
    }
  }

  function mostrarTareas() {
    // Limpiar HTML
    limpiarTareas();    
    totalPendientes();
    totalProceso();
    totalCompletadas();

    // Llenar arreglo de filtradas
    const arrayTareas = filtradas.length ? filtradas : tareas;

    // No hay tareas
    if (arrayTareas.length === 0) {
      const contenedorTareas = document.querySelector("#listado-tareas");

      const textoNoTareas = document.createElement("LI");
      textoNoTareas.textContent = "No hay tareas en este proyecto";
      textoNoTareas.classList.add("no-tareas");

      contenedorTareas.appendChild(textoNoTareas);
      return;
    }

    const estados = {
      0: "Pendiente",
      1: "En-Proceso",
      2: "Completo",
    };

    arrayTareas.forEach(tarea => {
      const contendedorTarea = document.createElement("LI");
      contendedorTarea.dataset.tareaId = tarea.id;
      contendedorTarea.classList.add("tarea");

      const nombreTarea = document.createElement("P");
      nombreTarea.textContent = tarea.nombre;
      // Llamado a la función de editar
      nombreTarea.ondblclick = () => mostrarFormulario(true, {...tarea});

      const acciones = document.createElement("DIV");
      acciones.classList.add("acciones");

      // Botones
      const btnEstadoTarea = document.createElement("BUTTON");
      btnEstadoTarea.classList.add("estado-tarea");
      btnEstadoTarea.classList.add(`${estados[tarea.estado].toLowerCase()}`);
      btnEstadoTarea.textContent = estados[tarea.estado];
      btnEstadoTarea.dataset.estadoTarea = tarea.estado;
      // Cambiar estado
      btnEstadoTarea.ondblclick = () => cambiarEstado({ ...tarea });

      const btnEliminarTarea = document.createElement("button");
      btnEliminarTarea.classList.add("eliminar-tarea");
      btnEliminarTarea.dataset.idTarea = tarea.id;
      btnEliminarTarea.textContent = "Eliminar";
      btnEliminarTarea.onclick = () => confirmarEliminarTarea({ ...tarea });

      acciones.appendChild(btnEstadoTarea);
      acciones.appendChild(btnEliminarTarea);

      contendedorTarea.appendChild(nombreTarea);
      contendedorTarea.appendChild(acciones);

      const listadoTareas = document.querySelector("#listado-tareas");
      listadoTareas.appendChild(contendedorTarea);
    });
  }

  function totalPendientes() {   
    
    // Filtrar por estado
    const totalPendientes = tareas.filter(tarea => tarea.estado === "0"); 
    
    const pendientesRadio = document.querySelector('#pendientes');    

    if(totalPendientes.length === 0){
      pendientesRadio.disabled = true;
    } else {
      pendientesRadio.disabled = false;
    }
  }
  function totalProceso() {   
    
    // Filtrar por estado
    const totalProceso = tareas.filter(tarea => tarea.estado === "1"); 
    
    const procesoRadio = document.querySelector('#proceso');    

    if(totalProceso.length === 0){
      procesoRadio.disabled = true;
    } else {
      procesoRadio.disabled = false;
    }
  }
  function totalCompletadas() {   
    
    // Filtrar por estado
    const totalCompletadas = tareas.filter(tarea => tarea.estado === "2"); 
    
    const completadasRadio = document.querySelector('#completadas');    

    if(totalCompletadas.length === 0){
      completadasRadio.disabled = true;
    } else {
      completadasRadio.disabled = false;
    }
  }

  function mostrarFormulario(editar = false, tarea = {}) {
    const modal = document.createElement("div");
    modal.classList.add("modal");
    modal.innerHTML = `
      <form class="formulario nueva-tarea">
        <legend>${editar ? "Edita tu tarea" : "Añade una nueva tarea"}</legend>
        <div class="campo">
          <label>Nombre:</label>
          <input
            type="text"
            name="nombre"
            placeholder="${
              tarea.nombre
                ? "Edita la tarea actual"
                : "Añade tarea a tu proyecto"
            } "
            id="nombre"
            value="${tarea.nombre || ""}"
          />
        </div>
        <div class="opciones">
            <input type="submit" class="submit-nueva-tarea" value= "${
              tarea.nombre ? "Actualizar Tarea" : "Añadir Tarea"
            }"/>
            <button type="button" class="cerrar-modal">Cancelar</button>
        </div>
      </form>
      `;

    setTimeout(() => {
      const formulario = document.querySelector(".formulario");
      formulario.classList.add("animar");
    }, 0);

    // Cerrar modal
    modal.addEventListener("click", (e) => {
      e.preventDefault();
      if (e.target.classList.contains("cerrar-modal")) {
        const formulario = document.querySelector(".formulario");
        formulario.classList.add("cerrar");
        setTimeout(() => {
          modal.remove();
        }, 300);
      }
      if (e.target.classList.contains("submit-nueva-tarea")) {
        const nombreTarea = document.querySelector("#nombre").value.trim();
        if (nombreTarea === "") {
          mostrarAlerta(
            "El nombre de la tarea es obligatorio",
            "error",
            document.querySelector(".formulario legend")
          );
          return;
        }

        if (editar) {
          // Editar tarea
          tarea.nombre = nombreTarea;
          actualizarTarea(tarea);
        } else {
          // Agregar tarea
          aggTarea(nombreTarea);
        }
      }
    });

    document.querySelector(".dashboard").appendChild(modal);
  }

  // Muestra un mensaje en la interfaz
  function mostrarAlerta(mensaje, tipo, ref) {
    // Si hay una alerta previa, no crear otra
    const alertaPrevia = document.querySelector(".alerta");
    if (alertaPrevia) {
      alertaPrevia.remove();
    }

    const alerta = document.createElement("div");
    alerta.classList.add("alerta", tipo);
    alerta.textContent = mensaje;

    // Insertar alerta antes de un elemento
    ref.parentElement.insertBefore(alerta, ref.nextElementSibling);

    // Quitar la alerta después de 3 segundos
    setTimeout(() => {
      alerta.remove();
    }, 3000);
  }

  // Agrega una tarea al DOM
  async function aggTarea(tarea) {    
    // Construir la petición
    const datos = new FormData();
    datos.append("nombre", tarea);
    datos.append("proyectoId", obtenerProyecto());    

    try {
      const url = "http://localhost:3000/api/tarea";
      const respuesta = await fetch(url, {
        method: "POST",
        body: datos,
      });

      const resultado = await respuesta.json();      

      mostrarAlerta(
        resultado.mensaje,
        resultado.tipo,
        document.querySelector(".formulario legend")
      );

      // Si se agrega la tarea al proyecto
      if (resultado.tipo === "exito") {
        const modal = document.querySelector(".modal");
        // Limpiar el formulario
        document.querySelector("#nombre").value = "";
        // setTimeout(() => {
        //   modal.remove();
        // }, 2000);
      }
      // Agregar la tarea al arreglo de tareas
      const tareaObj = {
        id: String(resultado.id),
        nombre: tarea,
        estado: "0",
        proyectoId: resultado.proyectoId,
        createdAt: resultado.createdAt
      };
      
      tareas = [...tareas, tareaObj];
      mostrarTareas();
    } catch (error) {
      console.log(error);
    }
  }

  function cambiarEstado(tarea) {
    if (tarea.estado === "0") {
      tarea.estado = "1";
    } else if (tarea.estado === "1") {
      tarea.estado = "2";
    } else {
      tarea.estado = "0";
    }

    actualizarTarea(tarea);
  }

  async function actualizarTarea(tarea) {
    const { estado, id, nombre, createdAt } = tarea;
    const datos = new FormData();
    datos.append("id", id);
    datos.append("nombre", nombre);
    datos.append("estado", estado);
    datos.append("proyectoId", obtenerProyecto());
    datos.append("createdAt", createdAt);

    try {
      const url = `/api/tarea/actualizar`;
      const respuesta = await fetch(url, {
        method: "POST",
        body: datos,
      });

      const resultado = await respuesta.json();

      if (resultado.respuesta.tipo === "exito") {
        Swal.fire('Actualizado!', resultado.respuesta.mensaje, "success")

        // Cerrar modal
        const modal = document.querySelector(".modal");
        if(modal){
          modal.remove();
        }

        // Actualizar el arreglo de tareas
        tareas = tareas.map((tareaMemoria) => {
          if (tareaMemoria.id === id) {
            tareaMemoria.estado = estado;
            tareaMemoria.nombre = nombre;
          }
          return tareaMemoria;
        });

        mostrarTareas();
      }
    } catch (error) {
      console.log(error);
    }
  }

  function confirmarEliminarTarea(tarea) {
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: "btn btn-success",
        cancelButton: "btn btn-danger",
      },
      buttonsStyling: true,
    });
    swalWithBootstrapButtons
      .fire({
        title: "¿Seguro desea eliminar la Tarea?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        reverseButtons: true,
      })
      .then((result) => {
        if (result.isConfirmed) {
          eliminarTarea(tarea);
          swalWithBootstrapButtons.fire({
            title: "Eliminado!",
            text: "Tu tarea ha sido eliminada.",
            icon: "success",
          });
        } else if (
          /* Read more about handling dismissals below */
          result.dismiss === Swal.DismissReason.cancel
        ) {
          swalWithBootstrapButtons.fire({
            title: "Cancelado",
            text: "Tu tarea sigue viva :)",
            icon: "error",
          });
        }
      });
  }

  async function eliminarTarea(tarea) {
    const { estado, id, nombre } = tarea;
    const datos = new FormData();
    datos.append("id", id);
    datos.append("nombre", nombre);
    datos.append("estado", estado);
    datos.append("proyectoId", obtenerProyecto());

    try {
      const url = `/api/tarea/eliminar`;
      const respuesta = await fetch(url, {
        method: "POST",
        body: datos,
      });

      const resultado = await respuesta.json();

      if (resultado.resultado) {
        // mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.contenedor-nueva-tarea'));        
        // Actualizar el arreglo de tareas
        tareas = tareas.filter((tareaMemoria) => tareaMemoria.id !== id);
        mostrarTareas();
      }
    } catch (error) {}
  }

  function obtenerProyecto() {
    const proyectoParams = new URLSearchParams(window.location.search);
    const proyecto = Object.fromEntries(proyectoParams.entries());
    return proyecto.id;
  }

  function limpiarTareas() {
    const listadoTareas = document.querySelector("#listado-tareas");
    while (listadoTareas.firstChild) {
      listadoTareas.removeChild(listadoTareas.firstChild);
    }
  }
})();
