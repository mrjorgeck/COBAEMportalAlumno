@extends('layouts.alumno')

@section('titulo', 'Aviso de privacidad')

{{--
    Texto oficial del aviso integral. FUENTE editorial: docs/aviso-privacidad.md
    (sección "Versión integral"). Este Blade lleva el texto VERBATIM formateado
    como HTML; si el plantel actualiza el aviso, se edita la fuente y se
    regenera esta vista. No leer el .md en runtime (ver auditoría E1-A/E2-A).
--}}

@section('contenido')
    <section class="rounded bg-white p-6 shadow-sm">
        <article class="prose prose-sm max-w-none text-gray-800">
            <h1>Aviso de Privacidad Integral del Portal Académico de Nuevo Ingreso</h1>
            <h2>Colegio de Bachilleres del Estado de Michoacán</h2>
            <h2>Plantel Ario de Rosales, Michoacán</h2>

            <p>El Colegio de Bachilleres del Estado de Michoacán, en su carácter de sujeto obligado y responsable institucional del tratamiento de datos personales, con la participación operativa del Plantel Ario de Rosales, Michoacán, pone a disposición de aspirantes, alumnas, alumnos, madres, padres, tutores, personal docente, personal administrativo y usuarios autorizados el presente Aviso de Privacidad Integral para el uso del Portal Académico de Nuevo Ingreso.</p>
            <p>El Portal Académico de Nuevo Ingreso tiene como finalidad apoyar el proceso de registro digital de aspirantes, generación de formato de inscripción en PDF, seguimiento de documentación, consulta de resultados de evaluación diagnóstica, información del curso propedéutico, asignación de grupo, matrícula, horario y publicación de avisos institucionales relacionados con el proceso de ingreso al Plantel Ario de Rosales.</p>
            <p>El presente aviso informa qué datos personales se recaban, para qué se utilizan, cómo se protegen, qué transferencias podrían realizarse conforme a la ley, quiénes pueden acceder a la información y cómo ejercer los derechos de acceso, rectificación, cancelación y oposición al tratamiento de datos personales.</p>

            <h2>I. Responsable institucional y área operativa local</h2>
            <p>El responsable institucional del tratamiento de los datos personales es el Colegio de Bachilleres del Estado de Michoacán.</p>
            <p>Para efectos operativos del Portal Académico de Nuevo Ingreso, el área administrativa responsable del tratamiento local, validación, actualización, consulta, seguimiento y resguardo operativo de la información será el Colegio de Bachilleres del Estado de Michoacán, Plantel Ario de Rosales, Michoacán, por conducto de su Dirección, áreas de control escolar, servicios escolares, personal administrativo autorizado y demás áreas que participen en el proceso de nuevo ingreso.</p>
            <p>El Plantel Ario de Rosales únicamente podrá tratar los datos personales para fines institucionales relacionados con el proceso de ingreso, seguimiento académico, control escolar, comunicación institucional, evaluación diagnóstica, asignación escolar, administración del portal, auditoría, soporte técnico, seguridad informática y cumplimiento de obligaciones legales aplicables.</p>
            <p>El personal directivo, administrativo, docente, técnico o de soporte que tenga acceso al portal deberá utilizar la información únicamente conforme a sus funciones, perfiles de acceso autorizados, deber de confidencialidad, principio de mínima información necesaria y trazabilidad de operaciones.</p>

            <h2>II. Finalidades principales del tratamiento de datos personales</h2>
            <p>Los datos personales recabados a través del Portal Académico de Nuevo Ingreso serán utilizados para las siguientes finalidades necesarias:</p>
            <ol>
                <li>Registrar digitalmente a aspirantes de nuevo ingreso.</li>
                <li>Validar la identidad del aspirante mediante CURP, fecha de nacimiento, folio u otros datos necesarios para el proceso.</li>
                <li>Generar, consultar y descargar el formato de inscripción en PDF.</li>
                <li>Integrar, actualizar y consultar el expediente digital del aspirante o alumno.</li>
                <li>Dar seguimiento a la entrega, revisión, validación o rechazo de documentación requerida.</li>
                <li>Administrar el proceso de inscripción o reinscripción, según corresponda.</li>
                <li>Generar y asignar folio interno del proceso de ingreso.</li>
                <li>Registrar y consultar folio de examen o ficha, cuando corresponda.</li>
                <li>Gestionar resultados de evaluación diagnóstica.</li>
                <li>Validar respuestas o resultados derivados de procesos de lectura, carga o importación de información.</li>
                <li>Publicar, consultar o notificar información relativa al curso propedéutico.</li>
                <li>Publicar, consultar o notificar grupo asignado, matrícula, turno, horario y avisos escolares.</li>
                <li>Dar seguimiento al avance del aspirante dentro del proceso de nuevo ingreso.</li>
                <li>Mantener comunicación institucional con aspirantes, alumnas, alumnos, madres, padres, tutores o representantes legales.</li>
                <li>Atender solicitudes, aclaraciones, incidencias, errores de captura, soporte técnico y trámites relacionados con el proceso.</li>
                <li>Generar reportes académicos, administrativos, estadísticos, de control escolar y planeación institucional.</li>
                <li>Realizar importaciones o exportaciones CSV necesarias para procesos administrativos o reportes oficiales.</li>
                <li>Administrar usuarios, roles, permisos, accesos y bitácoras del sistema.</li>
                <li>Prevenir accesos no autorizados, errores, alteraciones, pérdida de información o uso indebido del portal.</li>
                <li>Cumplir obligaciones legales, administrativas, educativas, archivísticas, de auditoría, transparencia, fiscalización o requerimientos de autoridad competente.</li>
            </ol>

            <h2>III. Finalidades secundarias u opcionales</h2>
            <p>De manera separada y sólo cuando corresponda, podrán utilizarse datos de imagen, fotografía, video, voz, participación en eventos, logros académicos, culturales, deportivos o testimonios para difusión institucional en medios oficiales, redes sociales, comunicados o materiales impresos del Colegio o del Plantel.</p>
            <p>Estas finalidades son opcionales y no serán condición para participar en el proceso de ingreso, consultar información, recibir atención académica o acceder a servicios escolares.</p>
            <p>Cuando se trate de menores de edad, la autorización para estas finalidades deberá otorgarse por la madre, padre, tutor o representante legal, conforme a la normativa aplicable. La negativa para el uso de imagen, voz o datos con fines de difusión no afectará el trámite académico ni la prestación del servicio educativo.</p>

            <h2>IV. Datos personales que podrán recabarse</h2>
            <p>Para cumplir las finalidades anteriores, el Portal Académico de Nuevo Ingreso podrá recabar, consultar, cargar, actualizar o resguardar los siguientes datos personales, según el trámite y el tipo de usuario.</p>
            <h3>A. Datos de aspirantes, alumnas y alumnos</h3>
            <ol>
                <li>Datos de identificación: nombre completo, CURP, fecha de nacimiento, edad, sexo, matrícula, folio interno, folio de examen, ficha, grupo, turno, ciclo escolar y fotografía institucional cuando resulte necesaria.</li>
                <li>Datos de contacto: domicilio, localidad, municipio, teléfono, correo electrónico y medios de contacto autorizados.</li>
                <li>Datos académicos: escuela de procedencia, tipo de secundaria, turno de secundaria, promedio, certificado, constancias, historial académico, resultados de evaluación diagnóstica, grupo asignado, horario, curso propedéutico y situación dentro del proceso de ingreso.</li>
                <li>Datos administrativos: estatus de registro, avance del proceso, documentos solicitados, documentos entregados, documentos pendientes, observaciones de revisión, validaciones, rechazos, aclaraciones, avisos y solicitudes.</li>
                <li>Datos de madre, padre, tutor o representante legal: nombre completo, parentesco, teléfono, correo electrónico, domicilio, identificación cuando resulte necesaria y datos de contacto para comunicación institucional o emergencias.</li>
                <li>Datos socioeconómicos: únicamente cuando sean necesarios para becas, apoyos, estudios socioeconómicos, programas institucionales o trámites que lo justifiquen.</li>
                <li>Datos de salud: únicamente cuando sean estrictamente necesarios para proteger la integridad del alumno, atender emergencias, gestionar seguros, afiliaciones, apoyos, actividades físicas, ajustes razonables o cumplir obligaciones institucionales.</li>
                <li>Datos de acceso y uso del portal: CURP utilizada para acceso, fecha y hora de ingreso, dirección IP, navegador, dispositivo, acciones realizadas, cambios registrados, descargas, consultas, solicitudes, validaciones y demás bitácoras necesarias para seguridad y auditoría.</li>
            </ol>
            <h3>B. Datos de madres, padres, tutores o representantes legales</h3>
            <ol>
                <li>Nombre completo.</li>
                <li>Parentesco o relación con el aspirante, alumna o alumno.</li>
                <li>Teléfono y correo electrónico.</li>
                <li>Domicilio, cuando sea necesario.</li>
                <li>Identificación oficial o documento de representación, cuando el trámite lo requiera.</li>
                <li>Datos de contacto para emergencias, seguimiento escolar o comunicación institucional.</li>
            </ol>
            <h3>C. Datos de personal docente, administrativo y usuarios internos</h3>
            <ol>
                <li>Datos de identificación: nombre completo, CURP, RFC cuando sea aplicable, número de empleado, firma, fotografía institucional y datos de adscripción.</li>
                <li>Datos laborales: cargo, función, área, grupos asignados, materias, horario, correo institucional, teléfono institucional y permisos de acceso.</li>
                <li>Datos académicos y profesionales: grado académico, cédula, certificaciones, constancias, capacitación y experiencia relacionada con funciones institucionales.</li>
                <li>Datos de uso del portal: usuario, roles, permisos, perfiles de acceso, fecha y hora de ingreso, dirección IP, acciones realizadas, validaciones, capturas, modificaciones, consultas, cargas, descargas, exportaciones y actividad administrativa o académica realizada dentro del sistema.</li>
            </ol>
            <h3>D. Datos sensibles</h3>
            <p>Podrán considerarse sensibles, entre otros, los datos de salud, discapacidad, condición física, información médica, psicológica, socioeconómica delicada, imagen cuando permita identificación, datos biométricos o cualquier otro dato que pueda afectar la esfera íntima de la persona titular o generar discriminación.</p>
            <p>El Portal Académico de Nuevo Ingreso no deberá solicitar datos biométricos, médicos, psicológicos, patrimoniales, familiares o socioeconómicos que no sean indispensables para una finalidad concreta, justificada y proporcional.</p>
            <p>El tratamiento de datos sensibles deberá limitarse a los casos estrictamente necesarios, observando medidas reforzadas de seguridad, acceso restringido, confidencialidad y justificación institucional.</p>

            <h2>V. Tratamiento de datos de menores de edad</h2>
            <p>Debido a que el Portal Académico de Nuevo Ingreso puede tratar datos personales de personas menores de edad, el Colegio y el Plantel deberán privilegiar en todo momento el interés superior de niñas, niños y adolescentes.</p>
            <p>El acceso, uso, consulta, validación, modificación o descarga de información de menores de edad quedará limitado al personal autorizado que requiera conocer dicha información para cumplir funciones de control escolar, seguimiento académico, orientación, seguridad, soporte, administración del portal o atención institucional.</p>
            <p>Cuando la normativa aplicable exija consentimiento, autorización o representación, ésta deberá ser otorgada por la madre, padre, tutor o representante legal. Cuando resulte adecuado, también podrá considerarse la opinión de la alumna o alumno, conforme a su edad, madurez y contexto escolar.</p>

            <h2>VI. Uso de CURP, folio y acceso al portal</h2>
            <p>El Portal Académico de Nuevo Ingreso podrá permitir el acceso del aspirante o alumno mediante CURP. Para consultar secciones sensibles, el sistema podrá solicitar un segundo dato de verificación, como fecha de nacimiento, folio, ficha u otro dato institucional.</p>
            <p>La CURP, folio, matrícula o cualquier dato de identificación escolar deberán utilizarse únicamente para autenticar, validar, consultar o dar seguimiento al proceso correspondiente. El sistema deberá impedir que la información de un aspirante o alumno sea consultada mediante parámetros inseguros, enlaces públicos no autorizados o accesos ajenos al proceso en sesión.</p>
            <p>La opción de recordar CURP en el dispositivo sólo deberá habilitarse con consentimiento del usuario y no deberá sustituir controles de seguridad para información sensible.</p>

            <h2>VII. Documentos, archivos y expedientes digitales</h2>
            <p>Los documentos cargados o generados en el portal, incluyendo formatos de inscripción, constancias, certificados, comprobantes, identificaciones, evidencias, resultados, archivos CSV, reportes o cualquier documento que contenga datos personales, deberán almacenarse en ubicaciones privadas y protegidas.</p>
            <p>Los archivos sensibles no deberán colocarse en carpetas públicas ni exponerse mediante enlaces abiertos. Su consulta o descarga deberá realizarse mediante controladores, permisos, políticas de acceso, autenticación, autorización y bitácoras.</p>
            <p>El personal autorizado sólo podrá consultar, descargar, modificar, validar o rechazar documentos cuando dicha acción sea necesaria para cumplir sus funciones institucionales.</p>

            <h2>VIII. Fundamento legal</h2>
            <p>El tratamiento de datos personales se realizará conforme a la Constitución Política de los Estados Unidos Mexicanos; la Ley General de Protección de Datos Personales en Posesión de Sujetos Obligados; la Ley de Protección de Datos Personales en Posesión de Sujetos Obligados del Estado de Michoacán de Ocampo que se encuentre vigente; la normativa aplicable en materia educativa; la normativa de transparencia, archivos, control escolar y rendición de cuentas; la normativa interna del Colegio de Bachilleres del Estado de Michoacán; y demás disposiciones jurídicas aplicables.</p>
            <p>El tratamiento de datos personales deberá sujetarse a los principios de licitud, finalidad, lealtad, consentimiento cuando corresponda, calidad, proporcionalidad, información y responsabilidad.</p>

            <h2>IX. Transferencias de datos personales</h2>
            <p>Los datos personales no serán vendidos, comercializados, publicados, difundidos ni utilizados para fines ajenos al servicio educativo.</p>
            <p>El Colegio y el Plantel podrán realizar transferencias de datos personales únicamente cuando sean necesarias para cumplir obligaciones legales, académicas, administrativas, educativas o institucionales, o cuando sean requeridas mediante solicitud fundada y motivada de autoridad competente.</p>
            <p>De manera enunciativa, los datos podrán ser comunicados, cuando resulte procedente, a autoridades educativas, áreas de control escolar, instituciones de seguridad social, instancias de becas, órganos fiscalizadores, autoridades jurisdiccionales, administrativas o investigadoras, autoridades de salud, plataformas institucionales oficiales y demás autoridades competentes que funden y motiven legalmente su solicitud.</p>
            <p>Cuando se utilicen servicios de hospedaje, desarrollo, mantenimiento, soporte técnico, respaldo, seguridad informática, lectura OMR, administración tecnológica o proveedores externos relacionados con el portal, dichos prestadores deberán actuar como encargados del tratamiento, no como propietarios de la información. Deberán obligarse por escrito a guardar confidencialidad, utilizar los datos únicamente conforme a instrucciones institucionales, aplicar medidas de seguridad, impedir accesos no autorizados y devolver, bloquear o eliminar la información cuando termine la relación de servicio o cuando así lo instruya el Colegio.</p>

            <h2>X. Medidas de seguridad</h2>
            <p>El Colegio y el Plantel deberán aplicar medidas administrativas, físicas y técnicas razonables para proteger los datos personales contra daño, pérdida, alteración, destrucción, uso, acceso, divulgación o tratamiento no autorizado.</p>
            <p>Entre dichas medidas podrán incluirse:</p>
            <ol>
                <li>Usuarios y contraseñas individuales.</li>
                <li>Roles y permisos por perfil.</li>
                <li>Acceso restringido por función.</li>
                <li>Validación adicional para secciones sensibles.</li>
                <li>Almacenamiento privado de documentos.</li>
                <li>Prohibición de colocar archivos sensibles en ubicaciones públicas.</li>
                <li>Bitácoras de acceso, consulta, modificación, validación, descarga y exportación.</li>
                <li>Registro de actividad relevante dentro del sistema.</li>
                <li>Encabezados o configuraciones para evitar almacenamiento indebido en caché de vistas con datos personales.</li>
                <li>Respaldo y recuperación de información.</li>
                <li>Restricción de descargas masivas.</li>
                <li>Revisión periódica de permisos.</li>
                <li>Protección de credenciales y variables de entorno.</li>
                <li>Capacitación y deber de confidencialidad del personal.</li>
                <li>Procedimientos de atención ante incidentes de seguridad.</li>
            </ol>
            <p>El personal docente, administrativo, técnico o directivo que tenga acceso al portal será responsable de utilizar la información únicamente para fines institucionales autorizados y deberá abstenerse de copiar, extraer, reenviar, publicar, fotografiar, descargar o compartir datos personales por medios no autorizados.</p>

            <h2>XI. Conservación de datos personales</h2>
            <p>Los datos personales serán conservados durante el tiempo necesario para cumplir las finalidades académicas, administrativas, legales, educativas, archivísticas, históricas, de auditoría, transparencia, rendición de cuentas o defensa jurídica que correspondan.</p>
            <p>Una vez cumplidas las finalidades y plazos aplicables, los datos deberán ser bloqueados, cancelados, suprimidos, depurados, disociados o resguardados conforme a la normativa de archivos, protección de datos personales y disposiciones institucionales aplicables.</p>
            <p>El portal deberá respetar la separación de información por ciclo escolar, evitando mezclar registros de distintos ciclos o reutilizar datos fuera de la finalidad autorizada.</p>

            <h2>XII. Derechos ARCO y revocación del consentimiento</h2>
            <p>La persona titular de los datos personales o, en su caso, su representante legal, madre, padre o tutor, podrá ejercer los derechos de acceso, rectificación, cancelación u oposición al tratamiento de sus datos personales, así como revocar el consentimiento cuando éste sea la base del tratamiento y resulte jurídicamente procedente.</p>
            <p>La solicitud deberá contener, cuando menos:</p>
            <ol>
                <li>Nombre de la persona titular.</li>
                <li>Documento que acredite identidad y, en su caso, representación legal.</li>
                <li>Descripción clara de los datos personales respecto de los que se busca ejercer algún derecho.</li>
                <li>Derecho que desea ejercer: acceso, rectificación, cancelación u oposición.</li>
                <li>Domicilio, correo electrónico u otro medio para recibir notificaciones.</li>
                <li>Elementos que faciliten la localización de la información.</li>
                <li>Firma de la persona solicitante, cuando corresponda.</li>
            </ol>
            <p>Las solicitudes podrán presentarse ante la Unidad de Transparencia del Colegio de Bachilleres del Estado de Michoacán, a través de la Plataforma Nacional de Transparencia, por los medios institucionales oficiales o directamente en las oficinas que el Colegio tenga habilitadas para tal efecto.</p>
            <p>También podrán presentarse consultas iniciales ante la Dirección del Plantel Ario de Rosales, sin perjuicio de que el trámite formal de derechos ARCO deba canalizarse por la Unidad de Transparencia competente.</p>

            <h2>XIII. Uso correcto del Portal Académico</h2>
            <p>El usuario se obliga a utilizar el Portal Académico de Nuevo Ingreso únicamente para fines escolares, académicos, administrativos o institucionales autorizados.</p>
            <p>Queda prohibido:</p>
            <ol>
                <li>Usar cuentas ajenas.</li>
                <li>Compartir contraseñas.</li>
                <li>Acceder a información sin autorización.</li>
                <li>Descargar, copiar o difundir datos personales sin justificación institucional.</li>
                <li>Publicar información de aspirantes, alumnas, alumnos, madres, padres, tutores, docentes o personal en redes sociales, grupos de mensajería o medios no autorizados.</li>
                <li>Alterar, eliminar, manipular o registrar información falsa.</li>
                <li>Utilizar los datos para fines particulares, comerciales, políticos, discriminatorios, de acoso, hostigamiento o cualquier otro fin ajeno al servicio educativo.</li>
                <li>Extraer bases de datos, listados, documentos, fotografías, resultados o reportes sin autorización expresa.</li>
                <li>Compartir capturas de pantalla que contengan datos personales.</li>
                <li>Conservar copias locales de información sin necesidad institucional justificada.</li>
            </ol>
            <p>El uso indebido del portal podrá dar lugar a la suspensión de accesos, investigación interna, responsabilidades administrativas, laborales, escolares, civiles, penales o las que resulten aplicables conforme a la normativa vigente.</p>

            <h2>XIV. Cambios al aviso de privacidad</h2>
            <p>El Colegio de Bachilleres del Estado de Michoacán podrá modificar el presente Aviso de Privacidad para atender cambios normativos, administrativos, tecnológicos, operativos o de seguridad.</p>
            <p>Las modificaciones serán dadas a conocer a través del Portal Académico de Nuevo Ingreso, medios institucionales del Plantel Ario de Rosales, portal institucional del Colegio o los medios oficiales que se determinen.</p>
            <p>Fecha de elaboración o actualización: {{ config('portal.aviso_privacidad_fecha_publicacion') }}.</p>
        </article>
    </section>
@endsection
