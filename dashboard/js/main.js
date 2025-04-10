let exp = false;
document.addEventListener("DOMContentLoaded", async () => {
  let total_enc = document.getElementById("total_enc");
  let est_dom = document.getElementById("est_dom");
  let est_men_dom = document.getElementById("est_men_dom");
  let container_blocks_exp = document.getElementById("learning_style_exp");
  let actor_expandir = document.getElementById("expandir_actor");
  let icon_exp = document.getElementById("icon_exp");

  const url_params = new URLSearchParams(window.location.search);
  const course_id = url_params.get('id'); // Esto obtiene el valor del parámetro "id" en la URL
  let form = new FormData();
  form.append("id", course_id);
  let request_get_metrics = await fetch("../blocks/learning_style/dashboard/api/get_metrics.php", {
    method: "POST",
    body: form
  });
  if (request_get_metrics.ok) {
    let response_get_metrics = await request_get_metrics.json();
    console.log(response_get_metrics)
    let total_curso = response_get_metrics["total_students_on_course"];
    let enc = response_get_metrics["total_students"];
    total_enc.innerText = Math.floor((enc / total_curso)*100) + "%";
    let ctx_bar_ = document.getElementById("distr_bar").getContext("2d");
    let ctx_pie = document.getElementById("distr_pie").getContext("2d");

    let llave_max = "";
    let llave_min = "";
    let estilo_hu = "";
    let estilo_men = "";
    let max_value = 0;
    let min_value = 0;

    //Calculo estilo dominante y menos dominante 
    for (let estilo in response_get_metrics["data"]) {
      
      if (response_get_metrics["data"][estilo] > max_value) {
        llave_max = estilo;
        max_value = response_get_metrics["data"][estilo];
      }
      if (response_get_metrics["data"][estilo] < min_value) {
        llave_min = estilo;
        min_value = response_get_metrics["data"][estilo];
      }
    }
    min_value = max_value;
    for (let estilo in response_get_metrics["data"]) {
      if (response_get_metrics["data"][estilo] < min_value) {
        llave_min = estilo;
        min_value = response_get_metrics["data"][estilo];
      }
    }
    //Determina estilo dominante
    switch (llave_max) {
      case "num_act":
        estilo_hu = "Activo";
        break;
      case "num_ref":
        estilo_hu = "Reflexivo";
        break;
      case "num_vis":
        estilo_hu = "Visual";
        break;
      case "num_vrb":
        estilo_hu = "Verbal";
        break;
      case "num_sen":
        estilo_hu = "Sensorial";
        break;
      case "num_int":
        estilo_hu = "Intuitivo";
        break;
      case "num_sec":
        estilo_hu = "Secuencial";
        break;
      case "num_glo":
        estilo_hu = "Global";
        break;
      default:
        estilo_hu = "N/A";
        break;
    }

    //Determina estilo menos dominante
    switch (llave_min) {
      case "num_act":
        estilo_men = "Activo";
        break;
      case "num_ref":
        estilo_men = "Reflexivo";
        break;
      case "num_vis":
        estilo_men = "Visual";
        break;
      case "num_vrb":
        estilo_men = "Verbal";
        break;
      case "num_sen":
        estilo_men = "Sensorial";
        break;
      case "num_int":
        estilo_men = "Intuitivo";
        break;
      case "num_sec":
        estilo_men = "Secuencial";
        break;
      case "num_glo":
        estilo_men = "Global";
        break;
      default:
        estilo_men = "N/A";
        break;
    }

    //Muestra resulstados
    console.log("estilo dominante ", llave_max);
    est_dom.innerText = estilo_hu;
    console.log("Estilo menos dominante: ", llave_min);
    est_men_dom.innerText = estilo_men;

    //Grafico
    let labels = [];
    let data = [];
    data.push(response_get_metrics["data"]["num_act"]);
    labels.push("Activo");
    data.push(response_get_metrics["data"]["num_ref"]);
    labels.push("Reflexivo");
    data.push(response_get_metrics["data"]["num_sen"]);
    labels.push("Sensitivo");
    data.push(response_get_metrics["data"]["num_int"]);
    labels.push("Intuitivo");
    data.push(response_get_metrics["data"]["num_vis"]);
    labels.push("Visual");
    data.push(response_get_metrics["data"]["num_vrb"]);
    labels.push("Verbal");
    data.push(response_get_metrics["data"]["num_sec"]);
    labels.push("Secuencial");
    data.push(response_get_metrics["data"]["num_glo"]);
    labels.push("Global");
    crearGrafico("pie", ctx_pie, labels, data, "Distribución de estilos de aprendizaje");
    crearGrafico("bar", ctx_bar_, labels, data, "Distribución de estilos de aprendizaje");
    ordenar_e_insertar(labels, data, container_blocks_exp);
    actor_expandir.addEventListener("click", () => {
      if (exp) {
        //se cierra el expandible
        exp = false;
        container_blocks_exp.className = "learning_style_exp_close";
        icon_exp.style.transform = "rotate(0deg)";
      } else {
        //se abre el expandible
        exp = true;
        container_blocks_exp.className = "learning_style_exp_open";
        icon_exp.style.transform = "rotate(180deg)";
      }
    })
  }
})
function crearGrafico(tipo, ctx, etiquetas, valores, titulo) {
  return new Chart(ctx, {
    type: tipo,
    data: {
      labels: etiquetas,
      datasets: [{
        label: "Valor",
        data: valores,
        backgroundColor: [
          "rgba(255, 99, 132, 0.2)",
          "rgba(54, 162, 235, 0.2)",
          "rgba(255, 206, 86, 0.2)",
          "rgba(75, 192, 192, 0.2)",
          "rgba(153, 102, 255, 0.2)",
          "rgba(255, 159, 64, 0.2)",
          "rgba(100, 221, 23, 0.2)",
          "rgba(255, 87, 34, 0.2)",
        ],
        borderColor: [
          "rgba(255, 99, 132, 1)",
          "rgba(54, 162, 235, 1)",
          "rgba(255, 206, 86, 1)",
          "rgba(75, 192, 192, 1)",
          "rgba(153, 102, 255, 1)",
          "rgba(255, 159, 64, 1)",
          "rgba(100, 221, 23, 1)",
          "rgba(255, 87, 34, 1)",
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        title: {
          display: true,
          text: titulo
        },
        legend: {
          display: false
        }
      }
    }
  });
}
function ordenar_e_insertar(array_cuali, array_cuant, container) {
  // Crear un array de pares de valores [número, nombre]
  let combinados = array_cuant.map((num, index) => [num, array_cuali[index]]);

  // Ordenar el array combinado basado en el valor numérico (primer valor de cada sub-array)
  combinados.sort((a, b) => b[0] - a[0]);

  // Descomponer el array combinado de nuevo en dos arrays ordenados
  numeros = combinados.map(item => item[0]);
  nombres = combinados.map(item => item[1]);
  console.log(numeros); 
  console.log(nombres);
  
  for(let i = 0; i < nombres.length; i++){
    let block_html = document.createElement("div");
    block_html.className = "flex";
    block_html.innerHTML = `<span>${nombres[i]}</span><span style="color: grey;">${numeros[i]}</span>`;
    container.appendChild(block_html);
  }
}
