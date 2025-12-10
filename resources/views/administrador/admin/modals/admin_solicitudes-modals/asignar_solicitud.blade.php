<div class="modal fade" id="asignarSolicitudModal" tabindex="-1" aria-labelledby="asignarSolicitudModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: #210240;">
                <h5 class="modal-title" id="asignarSolicitudModalLabel">Asignar Solicitud</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="asignarSolicitudForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="ticket_id" name="ticket_id">
                    <input type="hidden" name="service_status_id" id="service_status_id" value="2">
                    <div class="mb-3">
                        <label for="support_personal_id" class="form-label fw-bold">Asignar a:</label>
                        <select class="form-select select2-support-personal" id="support_personal_id" name="support_personal_id" required>
                            <option value="">Seleccionar personal de soporte</option>
                            @foreach($supportPersonals as $personal)
                                <option value="{{ $personal->id }}">
                                    {{ $personal->name }} {{ $personal->lastnames }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title fw-bold mb-3">Información de la Solicitud</h6>
                            
                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <strong>Solicita:</strong>
                                </div>
                                <div class="col-md-9">
                                    <span id="modal_employee_name">—</span>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <strong>Descripción:</strong>
                                </div>
                                <div class="col-md-9">
                                    <span id="modal_description">—</span>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <strong>Edificio:</strong>
                                </div>
                                <div class="col-md-9">
                                    <span id="modal_building">—</span>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-md-3">
                                    <strong>Departamento:</strong>
                                </div>
                                <div class="col-md-9">
                                    <span id="modal_department">—</span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Fecha recepción:</strong>
                                </div>
                                <div class="col-md-9">
                                    <span id="modal_created_at">—</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Asignar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>