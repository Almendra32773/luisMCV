<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Nuevo Préstamo</h1>
        <p class="text-muted mb-0">Registra un nuevo préstamo de libro</p>
    </div>
    <a href="/loans" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Volver
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="/loans">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?= \Core\Csrf::get() ?>">
            
            <div class="row">
                <!-- Selección de Socio -->
                <div class="col-md-6 mb-4">
                    <label for="member_id" class="form-label">Socio <span class="text-danger">*</span></label>
                    <select class="form-select" id="member_id" name="member_id" required>
                        <option value="">Seleccionar socio...</option>
                        <?php foreach ($members as $member): ?>
                            <option value="<?= $member['id'] ?>" 
                                    <?= ($_GET['member_id'] ?? '') == $member['id'] ? 'selected' : '' ?>
                                    data-max-loans="<?= $member['max_loans'] ?>"
                                    data-active-loans="<?= $member['active_loans'] ?>">
                                <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?> 
                                (<?= $member['member_code'] ?>)
                                <?php if ($member['active_loans'] >= $member['max_loans']): ?>
                                    ❌ Límite alcanzado
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text" id="member-info">
                        Seleccione un socio para ver información
                    </div>
                </div>

                <!-- Selección de Libro -->
                <div class="col-md-6 mb-4">
                    <label for="book_search" class="form-label">Buscar Libro</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="book_search" 
                               placeholder="Buscar por título, autor o ISBN...">
                        <button type="button" class="btn btn-outline-secondary" id="search-btn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    <div id="book-results" class="mt-2 d-none">
                        <!-- Los resultados aparecerán aquí -->
                    </div>
                </div>

                <!-- Información del libro seleccionado (hidden hasta seleccionar) -->
                <div id="selected-book-info" class="col-md-12 mb-4 d-none">
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 id="selected-book-title" class="mb-1"></h6>
                                    <small class="text-muted" id="selected-book-author"></small>
                                    <div class="mt-2">
                                        <span class="badge bg-info" id="selected-book-isbn"></span>
                                        <span class="badge bg-success ms-2" id="selected-book-copies"></span>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <input type="hidden" name="isbn" id="selected-isbn">
                                    <button type="button" class="btn btn-outline-danger" id="clear-book">
                                        <i class="bi bi-x-circle me-2"></i>Cambiar Libro
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selección de Copia -->
                <div id="copy-selection" class="col-md-12 mb-4 d-none">
                    <label class="form-label">Seleccionar Copia Disponible</label>
                    <div id="available-copies" class="row">
                        <!-- Las copias disponibles aparecerán aquí -->
                    </div>
                    <input type="hidden" name="copy_id" id="selected-copy-id">
                </div>

                <!-- Fechas y configuración -->
                <div class="col-md-6 mb-3">
                    <label for="loan_date" class="form-label">Fecha de Préstamo <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="loan_date" name="loan_date" 
                           value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="loan_days" class="form-label">Días de Préstamo</label>
                    <select class="form-select" id="loan_days" name="loan_days">
                        <option value="7">7 días (1 semana)</option>
                        <option value="15" selected>15 días (2 semanas)</option>
                        <option value="30">30 días (1 mes)</option>
                        <option value="45">45 días</option>
                    </select>
                </div>

                <!-- Fecha de vencimiento (calculada automáticamente) -->
                <div class="col-md-12 mb-3">
                    <label for="due_date" class="form-label">Fecha de Vencimiento</label>
                    <input type="date" class="form-control bg-light" id="due_date" name="due_date" 
                           readonly>
                </div>

                <!-- Notas -->
                <div class="col-md-12 mb-4">
                    <label for="notes" class="form-label">Notas del Préstamo</label>
                    <textarea class="form-control" id="notes" name="notes" 
                              rows="3" placeholder="Observaciones sobre el préstamo..."></textarea>
                </div>
            </div>

            <!-- Resumen y botones -->
            <div class="border-top pt-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3">Resumen del Préstamo</h6>
                        <dl class="row" id="loan-summary">
                            <dt class="col-sm-4">Socio:</dt>
                            <dd class="col-sm-8 text-muted">No seleccionado</dd>
                            
                            <dt class="col-sm-4">Libro:</dt>
                            <dd class="col-sm-8 text-muted">No seleccionado</dd>
                            
                            <dt class="col-sm-4">Copia:</dt>
                            <dd class="col-sm-8 text-muted">No seleccionada</dd>
                            
                            <dt class="col-sm-4">Duración:</dt>
                            <dd class="col-sm-8 text-muted">15 días</dd>
                            
                            <dt class="col-sm-4">Vencimiento:</dt>
                            <dd class="col-sm-8 text-muted"><?= date('d/m/Y', strtotime('+15 days')) ?></dd>
                        </dl>
                    </div>
                    
                    <div class="col-md-6 d-flex align-items-end justify-content-end">
                        <div class="d-flex gap-2">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="bi bi-eraser me-2"></i>Limpiar
                            </button>
                            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                                <i class="bi bi-save me-2"></i>Registrar Préstamo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const memberSelect = document.getElementById('member_id');
        const bookSearch = document.getElementById('book_search');
        const searchBtn = document.getElementById('search-btn');
        const bookResults = document.getElementById('book-results');
        const selectedBookInfo = document.getElementById('selected-book-info');
        const copySelection = document.getElementById('copy-selection');
        const availableCopiesDiv = document.getElementById('available-copies');
        const loanDateInput = document.getElementById('loan_date');
        const loanDaysSelect = document.getElementById('loan_days');
        const dueDateInput = document.getElementById('due_date');
        const submitBtn = document.getElementById('submit-btn');
        const loanSummary = document.getElementById('loan-summary');
        
        // Información del miembro seleccionado
        let selectedMember = null;
        let selectedBook = null;
        let selectedCopy = null;
        
        // Actualizar fecha de vencimiento
        function updateDueDate() {
            const loanDate = new Date(loanDateInput.value);
            const loanDays = parseInt(loanDaysSelect.value);
            const dueDate = new Date(loanDate);
            dueDate.setDate(dueDate.getDate() + loanDays);
            
            dueDateInput.value = dueDate.toISOString().split('T')[0];
            
            // Actualizar resumen
            updateLoanSummary();
        }
        
        // Actualizar resumen
        function updateLoanSummary() {
            const summaryItems = loanSummary.getElementsByTagName('dd');
            
            // Socio
            if (selectedMember) {
                summaryItems[0].textContent = selectedMember.name;
                summaryItems[0].className = 'col-sm-8';
            } else {
                summaryItems[0].textContent = 'No seleccionado';
                summaryItems[0].className = 'col-sm-8 text-muted';
            }
            
            // Libro
            if (selectedBook) {
                summaryItems[1].textContent = selectedBook.title;
                summaryItems[1].className = 'col-sm-8';
            } else {
                summaryItems[1].textContent = 'No seleccionado';
                summaryItems[1].className = 'col-sm-8 text-muted';
            }
            
            // Copia
            if (selectedCopy) {
                summaryItems[2].textContent = selectedCopy.code;
                summaryItems[2].className = 'col-sm-8';
            } else {
                summaryItems[2].textContent = 'No seleccionada';
                summaryItems[2].className = 'col-sm-8 text-muted';
            }
            
            // Duración y vencimiento
            summaryItems[3].textContent = loanDaysSelect.value + ' días';
            summaryItems[4].textContent = dueDateInput.value.split('-').reverse().join('/');
            
            // Habilitar/deshabilitar botón de envío
            submitBtn.disabled = !(selectedMember && selectedBook && selectedCopy);
        }
        
        // Event listeners
        loanDateInput.addEventListener('change', updateDueDate);
        loanDaysSelect.addEventListener('change', updateDueDate);
        
        // Inicializar
        updateDueDate();
        
        // Información del socio
        memberSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.value) {
                selectedMember = {
                    id: selectedOption.value,
                    name: selectedOption.text.split(' (')[0],
                    maxLoans: selectedOption.dataset.maxLoans,
                    activeLoans: selectedOption.dataset.activeLoans
                };
                
                const infoDiv = document.getElementById('member-info');
                infoDiv.innerHTML = `
                    <div class="alert alert-info mb-0 p-2">
                        <small>
                            <i class="bi bi-person-check me-1"></i>
                            ${selectedMember.name}<br>
                            <i class="bi bi-book me-1"></i>
                            Préstamos activos: ${selectedMember.activeLoans}/${selectedMember.maxLoans}
                        </small>
                    </div>
                `;
            } else {
                selectedMember = null;
                document.getElementById('member-info').textContent = 'Seleccione un socio para ver información';
            }
            
            updateLoanSummary();
        });
        
        // Búsqueda de libros
        searchBtn.addEventListener('click', searchBooks);
        bookSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchBooks();
            }
        });
        
        function searchBooks() {
            const query = bookSearch.value.trim();
            if (!query) return;
            
            fetch(`/api/books/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(books => {
                    bookResults.innerHTML = '';
                    bookResults.className = 'mt-2';
                    
                    if (books.length === 0) {
                        bookResults.innerHTML = `
                            <div class="alert alert-warning mb-0">
                                No se encontraron libros
                            </div>
                        `;
                        return;
                    }
                    
                    const list = document.createElement('div');
                    list.className = 'list-group';
                    
                    books.forEach(book => {
                        const item = document.createElement('a');
                        item.href = '#';
                        item.className = 'list-group-item list-group-item-action';
                        item.innerHTML = `
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${book.title}</h6>
                                <small class="text-muted">${book.available_copies} disp.</small>
                            </div>
                            <p class="mb-1 small">${book.author}</p>
                            <small class="text-muted">ISBN: ${book.isbn}</small>
                        `;
                        
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            selectBook(book);
                        });
                        
                        list.appendChild(item);
                    });
                    
                    bookResults.appendChild(list);
                })
                .catch(error => {
                    console.error('Error:', error);
                    bookResults.innerHTML = `
                        <div class="alert alert-danger mb-0">
                            Error al buscar libros
                        </div>
                    `;
                });
        }
        
        function selectBook(book) {
            selectedBook = book;
            
            // Mostrar información del libro
            document.getElementById('selected-book-title').textContent = book.title;
            document.getElementById('selected-book-author').textContent = book.author;
            document.getElementById('selected-book-isbn').textContent = `ISBN: ${book.isbn}`;
            document.getElementById('selected-book-copies').textContent = `${book.available_copies} copias disponibles`;
            document.getElementById('selected-isbn').value = book.isbn;
            
            selectedBookInfo.classList.remove('d-none');
            bookResults.innerHTML = '';
            bookResults.className = 'mt-2 d-none';
            bookSearch.value = '';
            
            // Cargar copias disponibles
            loadAvailableCopies(book.isbn);
            
            updateLoanSummary();
        }
        
        function loadAvailableCopies(isbn) {
            fetch(`/api/books/${isbn}/copies`)
                .then(response => response.json())
                .then(copies => {
                    copySelection.classList.remove('d-none');
                    availableCopiesDiv.innerHTML = '';
                    
                    if (copies.length === 0) {
                        availableCopiesDiv.innerHTML = `
                            <div class="alert alert-warning mb-0">
                                No hay copias disponibles
                            </div>
                        `;
                        return;
                    }
                    
                    copies.forEach(copy => {
                        const col = document.createElement('div');
                        col.className = 'col-md-3 col-6 mb-2';
                        
                        const card = document.createElement('div');
                        card.className = 'card h-100';
                        
                        const cardBody = document.createElement('div');
                        cardBody.className = 'card-body text-center';
                        
                        const title = document.createElement('h6');
                        title.className = 'card-title';
                        title.textContent = copy.copy_code;
                        
                        const selectBtn = document.createElement('button');
                        selectBtn.type = 'button';
                        selectBtn.className = 'btn btn-sm btn-outline-primary w-100 mt-2';
                        selectBtn.textContent = 'Seleccionar';
                        selectBtn.addEventListener('click', function() {
                            selectCopy(copy);
                            
                            // Remover selección anterior
                            document.querySelectorAll('.card.border-primary').forEach(card => {
                                card.classList.remove('border-primary');
                            });
                            
                            // Marcar como seleccionada
                            card.classList.add('border-primary');
                        });
                        
                        cardBody.appendChild(title);
                        cardBody.appendChild(selectBtn);
                        card.appendChild(cardBody);
                        col.appendChild(card);
                        availableCopiesDiv.appendChild(col);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    availableCopiesDiv.innerHTML = `
                        <div class="alert alert-danger mb-0">
                            Error al cargar copias
                        </div>
                    `;
                });
        }
        
        function selectCopy(copy) {
            selectedCopy = copy;
            document.getElementById('selected-copy-id').value = copy.id;
            updateLoanSummary();
        }
        
        // Limpiar selección de libro
        document.getElementById('clear-book').addEventListener('click', function() {
            selectedBook = null;
            selectedCopy = null;
            selectedBookInfo.classList.add('d-none');
            copySelection.classList.add('d-none');
            availableCopiesDiv.innerHTML = '';
            document.getElementById('selected-isbn').value = '';
            document.getElementById('selected-copy-id').value = '';
            updateLoanSummary();
        });
    });
</script>
<?php 
$content = ob_get_clean(); 
$title = 'Nuevo Préstamo';
require_once __DIR__ . '/../layouts/app.php'; 
?>