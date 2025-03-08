<?php namespace Adrilomart\Wedding\Components;

use Cms\Classes\ComponentBase;
use Adrilomart\Wedding\Models\Invitado;
use Adrilomart\Wedding\Models\Hijos;
use Response;
use Log;
use Input;
use Request;
use Mail;
use Flash;
use Redirect;
use Cms;


/**
 * Export Component
 *
 * @link https://docs.octobercms.com/3.x/extend/cms-components.html
 */
class Invitados extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Invitados',
            'description' => 'No description provided yet...'
        ];
    }

    /**
     * @link https://docs.octobercms.com/3.x/element/inspector-types.html
     */
    public function defineProperties()
    {
        return [];
    }
        
    public function onRun(){
        // log::info('asd');
    }
    
    public function onBoda(){

        $data = Input::all();
            
        $rules = [
            'nombre' => 'required',
            'apellidos' => 'required',
            'telf' => 'required',
            'email' => 'required|email',
            'autocar' => 'required',
            'hijos' => 'required'
        ];
        
        
        if ( $data['hijos'] == 'si' ){
            $rules['hijo'] = 'required';
        }
        
        $validate = Request::validate( $rules );
        
        $invitado = new Invitado;
        
        $invitado->nombre = $data['nombre'];
        $invitado->apellidos = $data['apellidos'];
        $invitado->telf = $data['telf'];
        $invitado->email = $data['email'];
        $invitado->autocar = $data['autocar'];
        $invitado->hijosas = $data['hijos'] ? 1 : 0;
        
        if ( isset( $data['alergias'] ) && $data['alergias'] ){
            $invitado->alergias = $data['alergias'];
        }
        
        if ( $data['hijos'] && !isset( $data['hijo'] )){
            Flash::warning('Añade un hijo para continuar.');
            return;
        }
        
        $invitado->save();
        
        if( $data['hijos'] && isset( $data['hijo'] ) ){
            foreach( $data['hijo'] as $hijo ){
                
                $kid = new Hijos;
                $kid->nombre = $hijo['nombre'];
                $kid->apellidos = $hijo['apellidos'];
                $kid->autocar = $hijo['autocar'];
                
                if ( isset( $hijo['alergias'] ) && $hijo['alergias'] ){
                    $kid->alergias = $hijo['alergias'];
                }
                $kid->invitado_id = $invitado->id;
                $kid->save();
            } 
        }
        
        // template para enviar a los host informando de una nueva inscripción.
        $to = ["jaelriaz@gmail.com","alejandro.carrillo.corts@gmail.com"];
        Mail::send( 'adrilomart.wedding::mails.nuevo_invitado', $data, function( $message ) use ( $to, $invitado ) {
            $message->to( $to );
        });

        $to_invitado = $data['email'];
        // template para mandar mail de confirmación a los invitados.
        Mail::send( 'adrilomart.wedding::mails.gracias_inscripcion', $data, function( $message ) use ( $to_invitado, $invitado ) {
            $message->to( $to_invitado );
        });
        
        return [ 
            '#container-form-response' => $this->renderPartial('response-form')
        ];
    }
    
    public function onExport(){

        $data = Input::all();

        $invitados = Invitado::whereHas( 'eventos', function ( $q ) use ( $data ){
            $q->where('evento_id', $data['event']);
        })->get();
        
        $evento = Evento::where( 'id', $data['event'] )->first();

        header('Content-Type: text/html','charset=utf-8');
        header('Content-Disposition: attachment; filename="'.$evento->slug.'_exportacion_'.date('y-m-d').'.csv');

        $document = fopen('./storage/temp/export/'.$evento->slug.'_exportacion_'.date('y-m-d').'.csv', 'w');
        $columns = array();

        $cols_export = [
            // tabla invitados
            'id','nombre','apellidos','empresa','cargo','email','telf','alergias',
            // tabla procedencia 
            'procedencia','esta_denegado',
            // tabla checkin
            'esta_asistencia_anulada','hijos confirmados','hijos asistentes','checkin'
        ];
        fputcsv($document, $cols_export,';');

        foreach( $invitados as $key => $row ){
            
            $fila = array();
            $fila['id'] = $row->id;
            $fila['nombre'] = $row->nombre;
            $fila['apellidos'] = $row->apellidos;
            $fila['empresa'] = $row->empresa;
            $fila['cargo'] = $row->cargo;
            $fila['email'] = $row->email;
            $fila['telf'] = $row->telf;
            $fila['alergias'] = $row->alergias;
            
            foreach ( $row->procedencia as $from ) {
                if ( $from->evento_id == $evento->id ) {
                    $fila['procedencia'] = $from->procedencia;
                    $fila['esta_denegado'] = $from->esta_denegado ? 'Si' : 'No';
                }
            }
            foreach ( $row->checkin as $entrance ){
                if( $entrance->evento_id == $evento->id ){
                    $fila['esta_asistencia_anulada'] = $from->esta_asistencia_anulada ? 'Asistencia anulada' : 'No' ;
                    $fila['hijos_confirmados'] = $entrance->hijos_confirmados;
                    $fila['hijos_asistentes'] = $entrance->hijos_asistentes;
                    if ( $entrance->updated_at > $evento->fecha_inicio && !$entrance->esta_asistencia_anulada ){
                        $fila['checkin'] =  date("Y-m-d H:i:s", strtotime( $entrance->updated_at . '+1 hour' ));
                    }else if ($entrance->esta_asistencia_anulada){
                        $fila['checkin'] = 'ASISTENCIA ANULADA';
                        
                    }else{
                        $fila['checkin'] = 'Checkin pendiente';
                    }
                }
            }

            fputcsv($document, $fila,';');

        }
        
        fclose($document);
        ob_flush();
        return Response::download('./storage/temp/export/'.$evento->slug.'_exportacion_'.date('y-m-d').'.csv');
        //->deleteFileAfterSend(true);
        
    }

}
