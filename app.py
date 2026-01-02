from flask import Flask, render_template, request
from config import get_connection

app = Flask(__name__)

@app.route("/personas/crear", methods=["GET", "POST"])
def crear_persona():
    mensaje = None

    if request.method == "POST":
        conn = get_connection()
        cursor = conn.cursor()

        sql = """
        INSERT INTO personas
        (nombres, apellidos, fecha_nacimiento, genero, telefono, direccion, email,
         estado, created_by, updated_by, created_at, updated_at, deleted_at)
        VALUES (%s,%s,%s,%s,%s,%s,%s,1,1,1,NOW(),NOW(),0)
        """

        datos = (
            request.form["nombres"],
            request.form["apellidos"],
            request.form["fecha_nacimiento"],
            request.form["genero"],
            request.form["telefono"],
            request.form["direccion"],
            request.form["email"]
        )

        cursor.execute(sql, datos)
        conn.commit()

        cursor.close()
        conn.close()

        mensaje = "✅ Persona registrada correctamente"

    return render_template("personas/login.html", mensaje=mensaje)

if __name__ == "__main__":
    app.run(debug=True)
