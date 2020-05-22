<?php

use app\models\Seguidores;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$url = Url::to(['seguidores/follow']);
$js = <<<EOT
var boton= $("#siguiendo");
boton.click(function(event) {
    event.preventDefault();
    $.ajax({
        method: 'GET',
        url: '$url',
        data: {
            'seguido_id': $model->id
        },
        success: function (data, code, jqXHR) {
            var texto= data[0]?"Dejar de seguir":"Seguir"
            boton.toggle("slide",1000);
            setTimeout( ()=> {
                boton.html(texto);
                $("#num").html(data[1] + " Seguidores")
            }, 1000);
            boton.toggle("slide",1000);
            boton.blur();
    }
    });
});
EOT;
$this->registerJs($js);
?>
<div class="usuarios-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Yii::$app->user->isGuest || (Yii::$app->user->id === $model->id) ? ""
            : Html::a(
                !Seguidores::estaSiguiendo($model->id) ?
                    'Seguir' : 'Dejar de seguir',
                ['seguidores/follow', 'seguido_id' => $model->id],
                ['class' => 'btn btn-info', 'id' => 'siguiendo'],

            )
        ?>
    </p>

    <div class="completados">
        <h2>
            <?= Html::a($model->total . " " . ($model->total === 1 ? "Juego Completado" : "Juegos Completados"), ['/completados/index', 'usuario_id' => $model->id]) ?>
        </h2>
    </div>

    <div id="linea-seguidores">
        <h3>
            <?= Html::a($siguiendo . " Siguiendo", ['/seguidores/index', 'seguidor_id' => $model->id]) ?>
        </h3>
        <h3>
            <?= Html::a($seguidores . " Seguidores", ['/seguidores/index-siguiendo', 'seguido_id' => $model->id], ['id' => 'num']) ?>
        </h3>
    </div>



    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-striped table-borderless detail-view'],
        'attributes' => [
            'username',
            'nombre',
            'apellidos',
            'email',
            'created_at:dateTime',
            'rol',
        ],
    ]);
    if (Yii::$app->user->id === $model->id) {

        echo (Html::a(
            'Modificar',
            ['update', 'id' => $model->id],
            ['class' => 'btn btn-success']
        ));
        echo (Html::a(
            'Cambiar Imagen',
            ['upload', 'id' => $model->id],
            ['class' => 'btn btn-success']
        ));
    }
    echo (Html::a(
        'Estadisticas',
        ['stats', 'id' => $model->id],
        ['class' => 'btn btn-success']
    ));
    ?>


    <?php if ($comentarios != null) ?>
    <div class="comentarios">
        <?php foreach ($comentarios as $key => $value) { ?>
            <div>
                <p>
                    <?= Html::encode($value->usuario->username) . ": " .
                        Html::encode($value->cuerpo) . " " .
                        Yii::$app->formatter->asDateTime($value->created_at) .
                        ($value->usuario_id === Yii::$app->user->id ?
                            Html::a(
                                '<span class="glyphicon glyphicon-pencil"></span>',
                                ['comentarios-usuarios/update', 'id' => $value->id]
                            ) . Html::a(
                                '<span class="glyphicon glyphicon-trash"></span>',
                                ['comentarios-usuarios/delete', 'id' => $value->id,],
                                ['data-method' => 'post']
                            ) : " ")
                    ?>
                </p>
            </div>
    </div>
<?php } ?>

<?php if (!Yii::$app->user->isGuest) : ?>
    <?php $form = ActiveForm::begin(['action' => ['comentarios-usuarios/comentar']]);
    ?>

    <?= $form->field($model2, 'cuerpo')->label(false) ?>
    <?= $form->field($model2, 'id')->hiddenInput(['value' => $model->id])->label(false) ?>


    <div class="form-group">
        <?= Html::submitButton('Comentar', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>

<?php endif ?>


</div>