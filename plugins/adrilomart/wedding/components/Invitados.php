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
        $debug = env('APP_DEBUG');

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
        if ( $debug ) {
            $to = 'adria.lop.mar@gmail.com';
        }else{
            $to = ["jaelriaz@gmail.com","alejandro.carrillo.corts@gmail.com"];
        }
        
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


        $invitados = Invitado::get();

        header('Content-Type: text/csv','charset=utf-8');
        header('Content-Disposition: attachment; filename=exportacion_invitados'.date('y-m-d').'.csv');

        $document = fopen('./export/exportacion_invitados'.date('y-m-d').'.csv', 'w');
        $columns = array();

        $cols_export = [
            // tabla invitados
            'id','nombre','apellidos','telf','email','autocar','alergias','hijos'
        ];

        fputcsv($document, $cols_export,';');

        foreach( $invitados as $key => $row ){
            
            $fila = array();
            $fila['id'] = $row->id;
            $fila['nombre'] = $row->nombre;
            $fila['apellidos'] = $row->apellidos;
            $fila['telf'] = $row->telf;
            $fila['email'] = $row->email;
            $fila['autocar'] = $row->autocar ? 'Sí' : 'No';
            $fila['alergias'] = $row->alergias;

            if ( $row->hijosas ){
                $fila['hijos'] ='';
                 foreach ( $row->ninos as $hijo ){
                     $fila['hijos'] = $fila['hijos'] . 'Nombre: '. $hijo->nombre . ' - Alergias: ' . $hijo->alergias .' - Autocar: ' . ($hijo->autocar ? 'Sí //// ' : 'No //// ');
                }
            }

            fputcsv($document, $fila,';');
        }

        fclose($document);
        ob_flush();

        return Response::download('./export/exportacion_invitados'.date('y-m-d').'.csv');
    }

}
